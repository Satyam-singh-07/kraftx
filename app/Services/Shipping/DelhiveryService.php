<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\ShipmentApiLog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        return filled($this->config['base_url'] ?? null) && filled($this->config['api_token'] ?? null);
    }

    protected function client(): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) ($this->config['base_url'] ?? ''), '/'))
            ->timeout((int) ($this->config['timeout'] ?? 15))
            ->retry(
                (int) ($this->config['retry_count'] ?? 2),
                (int) ($this->config['retry_sleep_ms'] ?? 250)
            )
            ->acceptJson();

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
            $response = $this->client()->send(strtoupper($method), $endpoint, [
                'json' => $payload,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $exception = $e;

            throw $e;
        } finally {
            $latencyMs = (int) round((microtime(true) - $started) * 1000);

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
}
