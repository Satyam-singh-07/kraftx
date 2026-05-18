<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use App\Services\Shipping\DTOs\ShipmentEligibilityResult;

class ShipmentEligibilityService
{
    public function __construct(
        protected ServiceabilityService $serviceability
    ) {
    }

    public function evaluate(Order $order): ShipmentEligibilityResult
    {
        return $this->evaluateForDraft($order);
    }

    public function evaluateForDraft(Order $order): ShipmentEligibilityResult
    {
        $reasons = [];
        $warnings = [];
        $order->loadMissing(['items.product', 'shipments.packages']);

        if (in_array($order->fulfillment_status, ['shipped', 'in_transit', 'delivered', 'rto'], true)) {
            $reasons[] = 'Order is already in a terminal or in-transit fulfillment state.';
        }

        if (in_array($order->status, ['cancelled', 'payment_failed'], true) || $order->fulfillment_status === 'cancelled') {
            $reasons[] = 'Cancelled or failed orders cannot create shipments.';
        }

        if ($order->payment_method === 'Prepaid' && $order->payment_status !== 'paid') {
            $reasons[] = 'Prepaid order payment is not verified.';
        }

        if ($order->payment_method === 'COD' && $order->status !== 'cod_confirmed') {
            $reasons[] = 'COD order is not confirmed.';
        }

        if ($order->shipments->whereIn('shipment_status', Shipment::ACTIVE_STATUSES)->isNotEmpty()) {
            $reasons[] = 'An active shipment already exists for this order.';
        }

        if (! $this->hasValidAddress($order)) {
            $reasons[] = 'Order is missing a valid delivery address, phone, or pincode.';
        }

        if ($order->items->isEmpty()) {
            $reasons[] = 'Order has no items.';
        }

        foreach ($order->items as $item) {
            if ($item->quantity < 1) {
                $reasons[] = 'Order contains an invalid item quantity.';
                break;
            }

            $product = $item->product;
            if (! $product) {
                $warnings[] = 'One or more products were deleted; use order item snapshots for fulfillment.';
                continue;
            }

            if ((float) $product->weight <= 0 || (float) $product->length <= 0 || (float) $product->width <= 0 || (float) $product->height <= 0) {
                $reasons[] = 'One or more products is missing package weight or dimensions.';
                break;
            }
        }

        return $reasons ? ShipmentEligibilityResult::blocked(array_values(array_unique($reasons)), $warnings) : ShipmentEligibilityResult::eligible($warnings);
    }

    public function evaluateForCreation(Order $order, Shipment $shipment): ShipmentEligibilityResult
    {
        $result = $this->evaluateForDraft($order);
        $reasons = $result->reasons;
        $warnings = $result->warnings;

        $order->loadMissing(['shipmentAttempts', 'shipments.packages']);
        $shipment->loadMissing('packages');

        $activeOtherShipment = $order->shipments
            ->filter(fn ($candidate) => $candidate->id !== $shipment->id)
            ->whereIn('shipment_status', Shipment::ACTIVE_STATUSES)
            ->first();

        if ($activeOtherShipment) {
            $reasons[] = 'Another active shipment already exists for this order.';
        }

        if (! in_array($shipment->shipment_status, [Shipment::STATUS_DRAFT, Shipment::STATUS_READY_TO_SHIP, Shipment::STATUS_FAILED], true)) {
            $reasons[] = 'Shipment is not in a creatable state.';
        }

        $package = $shipment->packages->first();
        if (! $package) {
            $reasons[] = 'Package details must be prepared before shipment creation.';
        } elseif (! $this->hasValidPackage($package)) {
            $reasons[] = 'Package weight and dimensions must be positive and realistic.';
        }

        $serviceability = $this->serviceability->cached($order->shipping_pincode);
        if (! $serviceability || ! $serviceability->isServiceable) {
            $reasons[] = 'Delhivery serviceability must be confirmed before shipment creation.';
        }

        if ($serviceability && $shipment->payment_mode === 'cod' && $serviceability->codAvailable === false) {
            $reasons[] = 'COD is not available for this delivery pincode.';
        }

        $inProgressAttempt = $order->shipmentAttempts
            ->where('provider', $shipment->provider)
            ->where('action', 'create_shipment')
            ->whereIn('status', ['processing'])
            ->first();

        if ($inProgressAttempt) {
            $reasons[] = 'A shipment creation attempt is already in progress.';
        }

        return $reasons ? ShipmentEligibilityResult::blocked(array_values(array_unique($reasons)), $warnings) : ShipmentEligibilityResult::eligible($warnings);
    }

    protected function hasValidAddress(Order $order): bool
    {
        return filled($order->customer_name)
            && preg_match('/^[6-9][0-9]{9}$/', (string) $order->customer_phone)
            && filled($order->shipping_address)
            && filled($order->shipping_city)
            && filled($order->shipping_state)
            && preg_match('/^[1-9][0-9]{5}$/', (string) $order->shipping_pincode);
    }

    protected function hasValidPackage(object $package): bool
    {
        return (float) $package->weight_kg > 0
            && (float) $package->weight_kg <= 50
            && (float) $package->length_cm > 0
            && (float) $package->length_cm <= 200
            && (float) $package->width_cm > 0
            && (float) $package->width_cm <= 200
            && (float) $package->height_cm > 0
            && (float) $package->height_cm <= 200;
    }
}
