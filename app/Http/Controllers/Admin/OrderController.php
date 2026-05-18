<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shipping\ShipmentEligibilityService;
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
    public function show($id, ShipmentEligibilityService $eligibility)
    {
        $order = Order::with([
            'items.product',
            'user',
            'shipments.packages',
            'shipments.events' => fn ($query) => $query->latest('event_time')->latest(),
            'shipments.apiLogs' => fn ($query) => $query->latest(),
        ])->findOrFail($id);

        return view('admin.orders.show', [
            'order' => $order,
            'shipmentEligibility' => $eligibility->evaluate($order),
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|string|in:pending,pending_payment,cod_confirmed,paid,payment_failed,processing,shipped,delivered,cancelled',
            'fulfillment_status' => 'nullable|string|in:pending,ready_to_ship,shipped,in_transit,delivered,rto,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->only(['status', 'fulfillment_status']));

        return back()->with('success', 'Order status updated successfully.');
    }
}
