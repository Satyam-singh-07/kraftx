<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Services\Shipping\DTOs\ShipmentEligibilityResult;

class ShipmentEligibilityService
{
    public function evaluate(Order $order): ShipmentEligibilityResult
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

        if ($order->shipments->whereNotIn('shipment_status', ['cancelled', 'failed'])->isNotEmpty()) {
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

    protected function hasValidAddress(Order $order): bool
    {
        return filled($order->customer_name)
            && preg_match('/^[6-9][0-9]{9}$/', (string) $order->customer_phone)
            && filled($order->shipping_address)
            && filled($order->shipping_city)
            && filled($order->shipping_state)
            && preg_match('/^[1-9][0-9]{5}$/', (string) $order->shipping_pincode);
    }
}
