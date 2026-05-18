<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShipmentExecutionService
{
    public function __construct(
        protected DelhiveryService $delhivery,
        protected ShipmentAttemptService $attempts,
        protected ShipmentEligibilityService $eligibility,
        protected ShipmentStatusService $statuses
    ) {
    }

    public function createDelhiveryShipment(Shipment $shipment): Shipment
    {
        $shipment->loadMissing(['order.shipments.packages', 'order.shipmentAttempts', 'packages']);
        $order = $shipment->order;

        if (! $order) {
            throw new RuntimeException('Shipment is missing its order.');
        }

        if (filled($shipment->awb)) {
            return $shipment;
        }

        $attempt = $this->lockAttempt($order, $shipment);

        try {
            $result = $this->eligibility->evaluateForCreation($order, $shipment);
            if (! $result->eligible) {
                throw new RuntimeException(implode(' ', $result->reasons));
            }

            if (in_array($shipment->shipment_status, [Shipment::STATUS_DRAFT, Shipment::STATUS_FAILED], true)) {
                $this->statuses->transition($shipment, Shipment::STATUS_READY_TO_SHIP, [
                    'creation_validated_at' => now()->toIso8601String(),
                ]);
                $shipment->refresh();
            }

            $this->statuses->transition($shipment, Shipment::STATUS_SHIPMENT_CREATING, [
                'last_creation_attempt_id' => $attempt->id,
            ]);

            $provider = $this->delhivery->createShipment($shipment);

            DB::transaction(function () use ($shipment, $attempt, $provider) {
                $shipment->refresh();
                $shipment->provider_shipment_id = $provider['provider_shipment_id'];
                $shipment->awb = $provider['awb'];
                $shipment->tracking_url = $provider['tracking_url'];
                $shipment->shipment_status_code = $provider['provider_status_code'];
                $shipment->serviceability_status = 'serviceable';
                $shipment->metadata = array_replace_recursive($shipment->metadata ?: [], [
                    'provider_status' => $provider['provider_status'],
                    'provider_message' => $provider['message'],
                    'provider_snapshot' => $provider['snapshot'],
                    'shipment_created_at' => now()->toIso8601String(),
                ]);
                $shipment->save();

                $shipment->packages()->update(['awb' => $provider['awb']]);

                $this->statuses->transition($shipment, Shipment::STATUS_SHIPMENT_CREATED);

                $attempt->update([
                    'status' => 'succeeded',
                    'last_error' => null,
                    'metadata' => array_replace_recursive($attempt->metadata ?: [], [
                        'awb' => $provider['awb'],
                        'provider_shipment_id' => $provider['provider_shipment_id'],
                        'succeeded_at' => now()->toIso8601String(),
                    ]),
                ]);

                $shipment->order()->update([
                    'fulfillment_status' => 'ready_to_ship',
                ]);
            });

            return $shipment->fresh(['packages', 'order']);
        } catch (\Throwable $e) {
            $attempt->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

            try {
                $this->statuses->transition($shipment->fresh(), Shipment::STATUS_FAILED, [
                    'failed_at' => now()->toIso8601String(),
                    'failure' => $e->getMessage(),
                ]);
            } catch (\Throwable $transitionException) {
                Log::warning('Shipment failure transition failed', [
                    'shipment_id' => $shipment->id,
                    'exception' => $transitionException::class,
                    'message' => $transitionException->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function generateLabel(Shipment $shipment): Shipment
    {
        $shipment->loadMissing('packages');

        if ($shipment->label_path) {
            return $shipment;
        }

        if (! in_array($shipment->shipment_status, [Shipment::STATUS_SHIPMENT_CREATED, Shipment::STATUS_LABEL_GENERATED], true)) {
            throw new RuntimeException('Label can only be generated after shipment creation.');
        }

        $label = $this->delhivery->generateLabel($shipment);

        $shipment->label_path = $label['label_path'];
        $shipment->label_generated_at = $label['generated_at'];
        $shipment->save();

        $this->statuses->transition($shipment, Shipment::STATUS_LABEL_GENERATED, [
            'label_generated_at' => $label['generated_at']->toIso8601String(),
        ]);

        return $shipment->fresh();
    }

    protected function lockAttempt(Order $order, Shipment $shipment): ShipmentAttempt
    {
        return DB::transaction(function () use ($order, $shipment) {
            $attempt = $this->attempts->prepare($order, $shipment->provider);
            $attempt = ShipmentAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();

            if ($attempt->status === 'processing') {
                throw new RuntimeException('A shipment creation attempt is already in progress.');
            }

            $attempt->update([
                'status' => 'processing',
                'attempt_count' => $attempt->attempt_count + 1,
                'shipment_id' => $shipment->id,
                'last_error' => null,
            ]);

            return $attempt;
        });
    }
}
