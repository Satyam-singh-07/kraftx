<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentPreparationService
{
    public function __construct(
        protected ShipmentStatusService $statuses
    ) {
    }

    public function prepareDraft(Order $order): Shipment
    {
        return DB::transaction(function () use ($order) {
            $order = Order::with(['items.product', 'shipments.packages'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            $existing = $order->shipments
                ->whereNotIn('shipment_status', [Shipment::STATUS_CANCELLED, Shipment::STATUS_FAILED])
                ->first();

            if ($existing) {
                return $existing;
            }

            $shipment = Shipment::create([
                'order_id' => $order->id,
                'provider' => 'delhivery',
                'shipment_status' => Shipment::STATUS_DRAFT,
                'payment_mode' => $this->paymentMode($order),
                'cod_amount' => $this->codAmount($order),
                'invoice_value' => $order->total_amount,
                'pickup_location_name' => config('shipping.providers.delhivery.pickup_location_name'),
                'serviceability_status' => 'pending',
                'created_by' => Auth::id(),
                'metadata' => [
                    'prepared_from' => 'admin_order_detail',
                    'prepared_at' => now()->toIso8601String(),
                ],
            ]);

            $this->upsertPackage($shipment, $this->prefillPackage($order));

            return $shipment->load('packages');
        });
    }

    public function updatePackage(Shipment $shipment, array $data): ShipmentPackage
    {
        if (filled($shipment->awb)) {
            throw new \RuntimeException('Package details cannot be changed after AWB generation.');
        }

        $package = $this->upsertPackage($shipment, [
            'weight_kg' => (float) $data['weight_kg'],
            'length_cm' => (float) $data['length_cm'],
            'width_cm' => (float) $data['width_cm'],
            'height_cm' => (float) $data['height_cm'],
            'metadata' => [
                'manual_override' => true,
                'updated_by' => Auth::id(),
                'updated_at' => now()->toIso8601String(),
            ],
        ]);

        if ($shipment->shipment_status === Shipment::STATUS_DRAFT) {
            $this->statuses->transition($shipment, Shipment::STATUS_READY_TO_SHIP, [
                'package_confirmed_at' => now()->toIso8601String(),
            ]);
        }

        return $package;
    }

    protected function upsertPackage(Shipment $shipment, array $data): ShipmentPackage
    {
        $weight = max((float) ($data['weight_kg'] ?? 0), 0);
        $length = max((float) ($data['length_cm'] ?? 0), 0);
        $width = max((float) ($data['width_cm'] ?? 0), 0);
        $height = max((float) ($data['height_cm'] ?? 0), 0);

        return ShipmentPackage::updateOrCreate(
            [
                'shipment_id' => $shipment->id,
                'package_number' => 1,
            ],
            [
                'weight_kg' => $weight,
                'length_cm' => $length,
                'width_cm' => $width,
                'height_cm' => $height,
                'volumetric_weight_kg' => $this->volumetricWeight($length, $width, $height),
                'metadata' => $data['metadata'] ?? [
                    'prefilled_from' => 'product_logistics_fields',
                ],
            ]
        );
    }

    protected function prefillPackage(Order $order): array
    {
        $weight = 0.0;
        $length = 0.0;
        $width = 0.0;
        $height = 0.0;

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $quantity = max((int) $item->quantity, 1);
            $weight += (float) $product->weight * $quantity;
            $length = max($length, (float) $product->length);
            $width = max($width, (float) $product->width);
            $height += (float) $product->height * $quantity;
        }

        return [
            'weight_kg' => round($weight, 3),
            'length_cm' => round($length, 2),
            'width_cm' => round($width, 2),
            'height_cm' => round($height, 2),
        ];
    }

    protected function volumetricWeight(float $length, float $width, float $height): float
    {
        return round(($length * $width * $height) / 5000, 3);
    }

    protected function paymentMode(Order $order): string
    {
        return strtolower((string) $order->payment_method) === 'cod' ? 'cod' : 'prepaid';
    }

    protected function codAmount(Order $order): float
    {
        return $this->paymentMode($order) === 'cod' ? (float) $order->total_amount : 0.0;
    }
}
