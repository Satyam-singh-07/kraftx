<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\ShipmentApiLog;
use App\Services\Shipping\DTOs\ServiceabilityResult;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use RuntimeException;

class DelhiveryService
{
    protected string $provider = 'delhivery';
    protected array $config;

    public function __construct()
    {
        $this->config = config('shipping.providers.delhivery', []);
    }

    public function configured(): bool
    {
        return $this->configurationErrors() === [];
    }

    public function configurationErrors(): array
    {
        $errors = [];
        $environment = (string) ($this->config['environment'] ?? '');
        $baseUrl = (string) ($this->config['base_url'] ?? '');

        if (! in_array($environment, ['staging', 'production'], true)) {
            $errors[] = 'Invalid Delhivery environment.';
        }

        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid Delhivery base URL.';
        }

        foreach (['api_token', 'client_name', 'pickup_location_name'] as $key) {
            if (! filled($this->config[$key] ?? null)) {
                $errors[] = 'Missing Delhivery '.$key.'.';
            }
        }

        if ($errors) {
            Log::warning('Delhivery configuration is incomplete', [
                'environment' => $environment ?: null,
                'base_url_configured' => filled($baseUrl),
                'api_token_configured' => filled($this->config['api_token'] ?? null),
                'client_name_configured' => filled($this->config['client_name'] ?? null),
                'pickup_location_configured' => filled($this->config['pickup_location_name'] ?? null),
                'errors' => $errors,
            ]);
        }

        return $errors;
    }

    public function checkServiceability(string $pincode, ?string $paymentMode = null): ServiceabilityResult
    {
        return $this->resolveServiceability($pincode, $paymentMode, false);
    }

    public function refreshServiceability(string $pincode, ?string $paymentMode = null): ServiceabilityResult
    {
        return $this->resolveServiceability($pincode, $paymentMode, true);
    }

    protected function resolveServiceability(string $pincode, ?string $paymentMode, bool $forceRefresh): ServiceabilityResult
    {
        $pincode = preg_replace('/\D+/', '', $pincode);
        if (! preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            throw new InvalidArgumentException('Invalid pincode.');
        }

        $cache = app(ServiceabilityService::class);
        $ttl = (int) ($this->config['serviceability_cache_ttl_minutes'] ?? 1440);
        $cached = null;

        try {
            $cached = $cache->cachedRecord($pincode, $this->provider);
        } catch (\Throwable $e) {
            Log::warning('Delhivery serviceability cache read failed', [
                'pincode' => $pincode,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
        }

        if (! $forceRefresh && $cached && $cache->isFresh($cached, $ttl)) {
            $this->logServiceabilityCache($pincode, true, true);

            return $cache->fromRecord($cached, true);
        }

        $this->logServiceabilityCache($pincode, false, true);

        if (! $this->configured()) {
            if ($cached) {
                return $cache->fromRecord($cached, true);
            }

            throw new RuntimeException('Delhivery serviceability is not configured.');
        }

        try {
            $response = $this->request('GET', '/c/api/pin-codes/json/', [
                'filter_codes' => $pincode,
            ]);

            if ($response->status() === 401) {
                throw new RuntimeException('Delhivery rejected the API token.');
            }

            if ($response->status() === 429) {
                throw new RuntimeException('Delhivery serviceability rate limit reached.');
            }

            if (! $response->successful()) {
                throw new RuntimeException('Delhivery serviceability request failed.');
            }

            $data = $response->json();
            if (! is_array($data)) {
                throw new RuntimeException('Delhivery returned a malformed serviceability response.');
            }

            $result = $this->normalizeServiceabilityResponse($pincode, $data, $paymentMode);

            try {
                $record = $cache->remember($result);

                return $cache->fromRecord($record, false);
            } catch (\Throwable $e) {
                Log::warning('Delhivery serviceability cache write failed', [
                    'pincode' => $pincode,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ]);

                return $result;
            }
        } catch (\Throwable $e) {
            Log::warning('Delhivery serviceability check failed', [
                'pincode' => $pincode,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'has_cached_fallback' => (bool) $cached,
            ]);

            if ($cached) {
                return $cache->fromRecord($cached, true);
            }

            throw $e;
        }
    }

    protected function normalizeServiceabilityResponse(string $pincode, array $payload, ?string $paymentMode = null): ServiceabilityResult
    {
        $deliveryCodes = Arr::get($payload, 'delivery_codes', []);
        $postalCode = null;

        if (is_array($deliveryCodes)) {
            foreach ($deliveryCodes as $candidate) {
                $candidatePostalCode = Arr::get($candidate, 'postal_code', $candidate);
                $candidatePin = (string) (Arr::get($candidatePostalCode, 'pin') ?? Arr::get($candidatePostalCode, 'pincode') ?? '');

                if ($candidatePin === $pincode || count($deliveryCodes) === 1) {
                    $postalCode = is_array($candidatePostalCode) ? $candidatePostalCode : [];
                    break;
                }
            }
        }

        $isServiceable = (bool) $postalCode;
        $codAvailable = $postalCode ? $this->truthy(Arr::get($postalCode, 'cod')) : false;
        $prepaidAvailable = $postalCode ? $this->truthy(Arr::get($postalCode, 'pre_paid', Arr::get($postalCode, 'prepaid'))) : false;
        $estimatedDays = $this->estimatedDays($postalCode ?: []);

        if ($paymentMode === 'cod' && $codAvailable === false) {
            $isServiceable = $prepaidAvailable === true;
        }

        return new ServiceabilityResult(
            provider: $this->provider,
            pincode: $pincode,
            isServiceable: $isServiceable,
            codAvailable: $codAvailable,
            prepaidAvailable: $prepaidAvailable,
            estimatedDays: $estimatedDays,
            responseSnapshot: $this->sanitizePayload([
                'delivery_codes_count' => is_array($deliveryCodes) ? count($deliveryCodes) : 0,
                'postal_code' => $postalCode,
                'payment_mode_checked' => $paymentMode,
            ]),
            checkedAt: now(),
            fromCache: false,
            message: app(ServiceabilityService::class)->message($isServiceable, $codAvailable, $prepaidAvailable, $estimatedDays)
        );
    }

    protected function client(): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) ($this->config['base_url'] ?? ''), '/'))
            ->timeout((int) ($this->config['timeout'] ?? 15))
            ->retry(
                (int) ($this->config['retry_count'] ?? 2),
                (int) ($this->config['retry_sleep_ms'] ?? 250),
                throw: false
            )
            ->acceptJson()
            ->asJson();

        if (filled($this->config['api_token'] ?? null)) {
            $client = $client->withHeaders([
                'Authorization' => 'Token '.$this->config['api_token'],
            ]);
        }

        return $client;
    }

    protected function request(string $method, string $endpoint, array $payload = [], ?Shipment $shipment = null): Response
    {
        $started = microtime(true);
        $response = null;
        $exception = null;

        Log::info('Shipping provider request started', [
            'provider' => $this->provider,
            'endpoint' => $endpoint,
            'method' => strtoupper($method),
            'shipment_id' => $shipment?->id,
        ]);

        try {
            $method = strtoupper($method);
            $response = $this->client()->send($method, $endpoint, $method === 'GET'
                ? ['query' => $payload]
                : ['json' => $payload]);

            return $response;
        } catch (\Throwable $e) {
            $exception = $e;

            throw $e;
        } finally {
            $latencyMs = (int) round((microtime(true) - $started) * 1000);

            try {
                ShipmentApiLog::create([
                    'shipment_id' => $shipment?->id,
                    'provider' => $this->provider,
                    'endpoint' => $endpoint,
                    'request_type' => strtoupper($method),
                    'response_code' => $response?->status(),
                    'latency_ms' => $latencyMs,
                    'success' => $response?->successful() ?? false,
                    'request_summary' => $this->sanitizePayload($payload),
                    'response_summary' => $response
                        ? $this->sanitizePayload($response->json() ?? ['body' => str($response->body())->limit(500)->toString()])
                        : ['exception' => $exception ? $exception::class : null],
                    'retry_count' => (int) ($this->config['retry_count'] ?? 0),
                ]);
            } catch (\Throwable $logException) {
                Log::warning('Shipping provider API log write failed', [
                    'provider' => $this->provider,
                    'endpoint' => $endpoint,
                    'exception' => $logException::class,
                    'message' => $logException->getMessage(),
                ]);
            }

            Log::info('Shipping provider request completed', [
                'provider' => $this->provider,
                'endpoint' => $endpoint,
                'method' => strtoupper($method),
                'shipment_id' => $shipment?->id,
                'response_code' => $response?->status(),
                'success' => $response?->successful() ?? false,
                'latency_ms' => $latencyMs,
            ]);
        }
    }

    protected function sanitizePayload(array $payload): array
    {
        $blocked = ['token', 'api_token', 'authorization', 'password', 'signature', 'payment_signature'];

        return collect($payload)->mapWithKeys(function ($value, $key) use ($blocked) {
            $normalizedKey = strtolower((string) $key);

            if (in_array($normalizedKey, $blocked, true) || str_contains($normalizedKey, 'token')) {
                return [$key => '[redacted]'];
            }

            if (in_array($normalizedKey, ['address', 'shipping_address', 'phone', 'email'], true)) {
                return [$key => '[redacted]'];
            }

            if (is_array($value)) {
                return [$key => $this->sanitizePayload($value)];
            }

            if (is_string($value) && strlen($value) > 500) {
                return [$key => substr($value, 0, 500)];
            }

            return [$key => $value];
        })->all();
    }

    protected function truthy(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['y', 'yes', 'true', '1'], true);
    }

    protected function estimatedDays(array $postalCode): ?int
    {
        foreach (['estimated_days', 'expected_delivery_days', 'days', 'ed', 'tat'] as $key) {
            $value = Arr::get($postalCode, $key);
            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }

    protected function logServiceabilityCache(string $pincode, bool $hit, bool $success): void
    {
        try {
            ShipmentApiLog::create([
                'provider' => $this->provider,
                'endpoint' => 'serviceability_cache',
                'request_type' => 'CACHE',
                'success' => $success,
                'request_summary' => [
                    'pincode' => $pincode,
                ],
                'response_summary' => [
                    'cache_hit' => $hit,
                ],
            ]);
        } catch (\Throwable $logException) {
            Log::warning('Serviceability cache log write failed', [
                'provider' => $this->provider,
                'pincode' => $pincode,
                'exception' => $logException::class,
                'message' => $logException->getMessage(),
            ]);
        }
    }
}
