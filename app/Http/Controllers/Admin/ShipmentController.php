<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\Shipping\ShipmentExecutionService;
use App\Services\Shipping\ShipmentPreparationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends Controller
{
    public function prepare(Order $order, ShipmentPreparationService $preparation): RedirectResponse
    {
        $shipment = $preparation->prepareDraft($order);

        return back()->with('success', 'Shipment draft is ready. Confirm package details before creating the Delhivery shipment.');
    }

    public function updatePackage(Request $request, Shipment $shipment, ShipmentPreparationService $preparation): RedirectResponse
    {
        $request->validate([
            'weight_kg' => 'required|numeric|min:0.001|max:50',
            'length_cm' => 'required|numeric|min:1|max:200',
            'width_cm' => 'required|numeric|min:1|max:200',
            'height_cm' => 'required|numeric|min:1|max:200',
        ]);

        try {
            $preparation->updatePackage($shipment, $request->only([
                'weight_kg',
                'length_cm',
                'width_cm',
                'height_cm',
            ]));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Package details confirmed.');
    }

    public function create(Shipment $shipment, ShipmentExecutionService $execution): RedirectResponse
    {
        try {
            $shipment = $execution->createDelhiveryShipment($shipment);

            return back()->with('success', 'Delhivery shipment created. AWB: '.$shipment->awb);
        } catch (\Throwable $e) {
            return back()->with('error', 'Shipment creation failed: '.$e->getMessage());
        }
    }

    public function generateLabel(Shipment $shipment, ShipmentExecutionService $execution): RedirectResponse
    {
        try {
            $execution->generateLabel($shipment);

            return back()->with('success', 'Shipping label generated.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Label generation failed: '.$e->getMessage());
        }
    }

    public function downloadLabel(Shipment $shipment): Response|RedirectResponse
    {
        return $this->labelResponse($shipment, false);
    }

    public function printLabel(Shipment $shipment): Response|RedirectResponse
    {
        return $this->labelResponse($shipment, true);
    }

    protected function labelResponse(Shipment $shipment, bool $inline): Response|RedirectResponse
    {
        if (! $shipment->label_path) {
            return back()->with('error', 'No label has been generated for this shipment yet.');
        }

        if (filter_var($shipment->label_path, FILTER_VALIDATE_URL)) {
            return redirect()->away($shipment->label_path);
        }

        if (! Storage::disk('local')->exists($shipment->label_path)) {
            return back()->with('error', 'Stored label file could not be found.');
        }

        $filename = 'delhivery-label-'.$shipment->awb.'.pdf';

        return $inline
            ? response(Storage::disk('local')->get($shipment->label_path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ])
            : Storage::disk('local')->download($shipment->label_path, $filename);
    }
}
