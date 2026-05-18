<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\ShipmentEvent;
use InvalidArgumentException;

class ShipmentStatusService
{
    protected array $transitions = [
        Shipment::STATUS_DRAFT => [
            Shipment::STATUS_READY_TO_SHIP,
            Shipment::STATUS_CANCELLED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_READY_TO_SHIP => [
            Shipment::STATUS_SHIPMENT_CREATING,
            Shipment::STATUS_CANCELLED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_SHIPMENT_CREATING => [
            Shipment::STATUS_SHIPMENT_CREATED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_SHIPMENT_CREATED => [
            Shipment::STATUS_LABEL_GENERATED,
            Shipment::STATUS_PICKUP_PENDING,
            Shipment::STATUS_CANCELLED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_LABEL_GENERATED => [
            Shipment::STATUS_PICKUP_PENDING,
            Shipment::STATUS_CANCELLED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_PICKUP_PENDING => [
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_CANCELLED,
            Shipment::STATUS_FAILED,
        ],
        Shipment::STATUS_PICKED_UP => [
            Shipment::STATUS_SHIPPED,
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_RTO,
        ],
        Shipment::STATUS_SHIPPED => [
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_DELIVERED,
            Shipment::STATUS_RTO,
        ],
        Shipment::STATUS_IN_TRANSIT => [
            Shipment::STATUS_DELIVERED,
            Shipment::STATUS_RTO,
        ],
        Shipment::STATUS_FAILED => [
            Shipment::STATUS_READY_TO_SHIP,
            Shipment::STATUS_CANCELLED,
        ],
    ];

    public function canTransition(Shipment $shipment, string $status): bool
    {
        if ($shipment->shipment_status === $status) {
            return true;
        }

        return in_array($status, $this->transitions[$shipment->shipment_status] ?? [], true);
    }

    public function transition(Shipment $shipment, string $status, array $metadata = []): Shipment
    {
        if (! $this->canTransition($shipment, $status)) {
            throw new InvalidArgumentException("Invalid shipment transition from {$shipment->shipment_status} to {$status}.");
        }

        $from = $shipment->shipment_status;
        $existingMetadata = $shipment->metadata ?: [];
        $shipment->shipment_status = $status;
        $shipment->metadata = array_replace_recursive($existingMetadata, $metadata);

        if ($status === Shipment::STATUS_CANCELLED) {
            $shipment->cancelled_at = now();
        }

        if ($status === Shipment::STATUS_SHIPPED && ! $shipment->shipped_at) {
            $shipment->shipped_at = now();
        }

        if ($status === Shipment::STATUS_DELIVERED && ! $shipment->delivered_at) {
            $shipment->delivered_at = now();
        }

        $shipment->save();

        ShipmentEvent::create([
            'shipment_id' => $shipment->id,
            'provider' => $shipment->provider,
            'event_type' => 'internal_transition',
            'raw_status' => $status,
            'normalized_status' => $status,
            'event_time' => now(),
            'payload_hash' => hash('sha256', implode('|', [
                $shipment->id,
                $shipment->provider,
                $status,
                microtime(true),
            ])),
            'raw_payload' => [
                'from' => $from,
                'to' => $status,
                'metadata' => $metadata,
            ],
            'is_duplicate' => false,
        ]);

        return $shipment;
    }
}
