<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shipping\DelhiveryService;
use App\Services\Shipping\ServiceabilityService;
use App\Services\Shipping\ShipmentEligibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::query()->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order, ShipmentEligibilityService $eligibility, ServiceabilityService $serviceability)
    {
        $order = Order::with([
            'items.product',
            'user',
            'shipments.packages',
            'shipments.events' => fn ($query) => $query->latest('event_time')->latest(),
            'shipments.apiLogs' => fn ($query) => $query->latest(),
        ])->findOrFail($order->id);

        return view('admin.orders.show', [
            'order' => $order,
            'shipmentEligibility' => $eligibility->evaluate($order),
            'serviceability' => $serviceability->cached($order->shipping_pincode),
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order, DelhiveryService $delhivery)
    {
        $request->validate([
            'status' => 'nullable|string|in:pending,pending_payment,cod_confirmed,paid,payment_failed,processing,shipped,delivered,cancelled',
            'fulfillment_status' => 'nullable|string|in:pending,ready_to_ship,shipped,in_transit,delivered,rto,cancelled',
        ]);

        if ($request->input('fulfillment_status') === 'ready_to_ship') {
            try {
                $result = $delhivery->checkServiceability($order->shipping_pincode, $order->payment_method === 'COD' ? 'cod' : 'prepaid');

                if (! $result->isServiceable) {
                    return back()->with('error', 'This order cannot be marked ready to ship because the pincode is not serviceable.');
                }

                if ($order->payment_method === 'COD' && $result->codAvailable === false) {
                    return back()->with('error', 'This COD order cannot be marked ready to ship because COD is not available for this pincode.');
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Serviceability must be confirmed before marking this order ready to ship.');
            }
        }

        $order->update($request->only(['status', 'fulfillment_status']));

        return back()->with('success', 'Order status updated successfully.');
    }

    public function recheckServiceability(Order $order, DelhiveryService $delhivery): RedirectResponse
    {
        try {
            $result = $delhivery->refreshServiceability(
                $order->shipping_pincode,
                $order->payment_method === 'COD' ? 'cod' : 'prepaid'
            );

            return back()->with(
                $result->isServiceable ? 'success' : 'error',
                $result->message ?: 'Serviceability check completed.'
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Serviceability check failed. Please verify Delhivery configuration or try again later.');
        }
    }
}
