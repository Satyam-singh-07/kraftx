<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\ShipmentApiLog;
use App\Models\ShipmentPackage;
use App\Services\Shipping\DTOs\ServiceabilityResult;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    public function createShipment(Shipment $shipment): array
    {
        $shipment->loadMissing(['order.items.product', 'packages']);

        if (! $this->configured()) {
            throw new RuntimeException('Delhivery shipment creation is not configured.');
        }

        $payload = $this->buildShipmentPayload($shipment);
        $response = $this->request('POST', '/api/cmu/create.json', [
            'format' => 'json',
            'data' => json_encode($payload, JSON_UNESCAPED_SLASHES),
        ], $shipment, 'form');

        if ($response->status() === 401) {
            throw new RuntimeException('Delhivery rejected the API token.');
        }

        if (! $response->successful()) {
            throw new RuntimeException('Delhivery shipment creation request failed.');
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new RuntimeException('Delhivery returned a malformed shipment creation response.');
        }

        $normalized = $this->normalizeShipmentCreationResponse($data);

        if (! $normalized['success']) {
            throw new RuntimeException($normalized['message'] ?: 'Delhivery rejected the shipment payload.');
        }

        return $normalized;
    }

    public function generateLabel(Shipment $shipment, string $size = '4R'): array
    {
        if (! filled($shipment->awb)) {
            throw new InvalidArgumentException('Shipment AWB is required before label generation.');
        }

        $response = $this->request('GET', '/api/p/packing_slip', [
            'wbns' => $shipment->awb,
            'pdf' => 'true',
            'pdf_size' => $size,
        ], $shipment);

        if ($response->status() === 401) {
            throw new RuntimeException('Delhivery rejected the API token.');
        }

        if (! $response->successful()) {
            throw new RuntimeException('Delhivery label request failed.');
        }

        $path = $this->storeLabelResponse($shipment, $response);

        return [
            'label_path' => $path,
            'generated_at' => now(),
        ];
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

    protected function buildShipmentPayload(Shipment $shipment): array
    {
        $order = $shipment->order;
        $package = $shipment->packages->first();

        if (! $order || ! $package) {
            throw new InvalidArgumentException('Shipment requires an order and package before provider creation.');
        }

        return [
            'shipments' => [
                [
                    'name' => $this->cleanText($order->customer_name, 80),
                    'add' => $this->cleanText($order->shipping_address, 250),
                    'pin' => preg_replace('/\D+/', '', (string) $order->shipping_pincode),
                    'city' => $this->cleanText($order->shipping_city, 50),
                    'state' => $this->cleanText($order->shipping_state, 50),
                    'country' => 'India',
                    'phone' => $this->normalizeIndianPhone($order->customer_phone),
                    'order' => $order->order_number,
                    'payment_mode' => $shipment->payment_mode === 'cod' ? 'COD' : 'Prepaid',
                    'cod_amount' => $shipment->payment_mode === 'cod' ? (string) $shipment->cod_amount : '',
                    'total_amount' => (string) $shipment->invoice_value,
                    'products_desc' => $this->cleanText($this->productDescription($order), 300),
                    'hsn_code' => $this->cleanText($this->hsnCodes($order), 120),
                    'quantity' => (string) $order->items->sum('quantity'),
                    'weight' => (string) $this->grams($package),
                    'shipment_length' => (string) round((float) $package->length_cm, 2),
                    'shipment_width' => (string) round((float) $package->width_cm, 2),
                    'shipment_height' => (string) round((float) $package->height_cm, 2),
                    'shipping_mode' => 'Surface',
                    'seller_name' => config('app.name', 'KraftX'),
                    'seller_inv' => $order->order_number,
                    'seller_add' => $this->cleanText((string) config('shipping.providers.delhivery.pickup_location_name'), 120),
                    'order_date' => optional($order->created_at)->format('Y-m-d H:i:s'),
                    'waybill' => '',
                    'address_type' => '',
                ],
            ],
            'pickup_location' => [
                'name' => (string) $this->config['pickup_location_name'],
            ],
        ];
    }

    protected function normalizeShipmentCreationResponse(array $data): array
    {
        $package = collect(Arr::get($data, 'packages', []))->first();
        $success = (bool) Arr::get($data, 'success', false) && ! (bool) Arr::get($data, 'error', false);

        if (is_array($package)) {
            $status = strtolower((string) Arr::get($package, 'status', ''));
            $success = $success || in_array($status, ['success', 'succeeded'], true);
        }

        $awb = is_array($package) ? (string) (Arr::get($package, 'waybill') ?: Arr::get($package, 'wbn')) : '';
        $providerShipmentId = (string) (Arr::get($data, 'upload_wbn') ?: Arr::get($data, 'upload_wbn_number') ?: $awb);
        $message = $this->providerMessage($data);

        return [
            'success' => $success && filled($awb),
            'provider_shipment_id' => $providerShipmentId ?: null,
            'awb' => $awb ?: null,
            'tracking_url' => $awb ? 'https://www.delhivery.com/track/package/'.$awb : null,
            'provider_status' => is_array($package) ? Arr::get($package, 'status') : null,
            'provider_status_code' => is_array($package) ? Arr::get($package, 'status_code') : null,
            'message' => $message,
            'snapshot' => $this->sanitizePayload($data),
        ];
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

    protected function request(string $method, string $endpoint, array $payload = [], ?Shipment $shipment = null, string $bodyType = 'json'): Response
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
            $client = $this->client();
            $options = $method === 'GET'
                ? ['query' => $payload]
                : ($bodyType === 'form' ? ['form_params' => $payload] : ['json' => $payload]);

            if ($bodyType === 'form' && $method !== 'GET') {
                $client = $client->asForm();
            }

            $response = $client->send($method, $endpoint, $options);

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

            if ($normalizedKey === 'data' && is_string($value)) {
                $decoded = json_decode($value, true);

                return [$key => is_array($decoded) ? $this->sanitizePayload($decoded) : '[provider_payload]'];
            }

            if (in_array($normalizedKey, ['add', 'address', 'shipping_address', 'phone', 'email', 'name'], true)) {
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

    protected function storeLabelResponse(Shipment $shipment, Response $response): string
    {
        $contentType = strtolower($response->header('Content-Type', ''));
        $body = $response->body();

        if (str_contains($contentType, 'pdf') || str_starts_with($body, '%PDF')) {
            $path = 'shipment-labels/delhivery/'.$shipment->awb.'.pdf';
            Storage::disk('local')->put($path, $body);

            return $path;
        }

        $data = $response->json();
        if (is_array($data)) {
            $url = $this->findUrl($data);
            if ($url) {
                return $url;
            }

            $path = 'shipment-labels/delhivery/'.$shipment->awb.'.json';
            Storage::disk('local')->put($path, json_encode($this->sanitizePayload($data), JSON_PRETTY_PRINT));

            return $path;
        }

        throw new RuntimeException('Delhivery returned a malformed label response.');
    }

    protected function findUrl(array $payload): ?string
    {
        foreach ($payload as $value) {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }

            if (is_array($value)) {
                $url = $this->findUrl($value);
                if ($url) {
                    return $url;
                }
            }
        }

        return null;
    }

    protected function grams(ShipmentPackage $package): int
    {
        return max(1, (int) round((float) $package->weight_kg * 1000));
    }

    protected function productDescription($order): string
    {
        return $order->items
            ->map(fn ($item) => $item->name.' x '.$item->quantity)
            ->implode(', ');
    }

    protected function hsnCodes($order): string
    {
        return $order->items
            ->map(fn ($item) => $item->product?->hsn_code)
            ->filter()
            ->unique()
            ->implode(',');
    }

    protected function providerMessage(array $payload): ?string
    {
        $remarks = Arr::get($payload, 'rmk') ?: Arr::get($payload, 'remarks');
        if (is_array($remarks)) {
            return implode(' ', array_filter($remarks));
        }

        if (is_string($remarks) && $remarks !== '') {
            return $remarks;
        }

        $package = collect(Arr::get($payload, 'packages', []))->first();
        if (is_array($package)) {
            $packageRemarks = Arr::get($package, 'remarks');
            if (is_array($packageRemarks)) {
                return implode(' ', array_filter($packageRemarks));
            }

            return is_string($packageRemarks) ? $packageRemarks : null;
        }

        return null;
    }

    protected function cleanText(?string $value, int $limit): string
    {
        $value = preg_replace('/[&#%;\\\\]+/', ' ', (string) $value);
        $value = preg_replace('/\s+/', ' ', trim($value));

        return mb_substr($value, 0, $limit);
    }

    protected function normalizeIndianPhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
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
