<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shipping\DelhiveryService;
use App\Services\Shipping\ServiceabilityService;
use App\Services\Shipping\ShipmentEligibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = $this->filteredOrdersQuery($request)->latest();

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $validated = $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:80'],
        ], [
            'date_from.required' => 'Please select a report start date.',
            'date_to.required' => 'Please select a report end date.',
            'date_to.after_or_equal' => 'Report end date must be after the start date.',
        ]);

        $fileName = 'orders-report-' . $validated['date_from'] . '-to-' . $validated['date_to'] . '.xls';
        $orders = $this->filteredOrdersQuery($request)
            ->with(['items.product', 'items.variant'])
            ->oldest('created_at');

        return response()->streamDownload(function () use ($orders) {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<thead><tr>';

            foreach ($this->exportHeadings() as $heading) {
                echo '<th>' . e($heading) . '</th>';
            }

            echo '</tr></thead><tbody>';

            $orders->chunk(200, function ($chunk) {
                foreach ($chunk as $order) {
                    echo '<tr>';

                    foreach ($this->exportRow($order) as $value) {
                        echo '<td>' . e($value) . '</td>';
                    }

                    echo '</tr>';
                }
            });

            echo '</tbody></table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order, ShipmentEligibilityService $eligibility, ServiceabilityService $serviceability)
    {
        $order = Order::with([
            'items.product',
            'items.variant',
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

    protected function filteredOrdersQuery(Request $request)
    {
        $query = Order::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from')->toDateString());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to')->toDateString());
        }

        return $query;
    }

    protected function exportHeadings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Order Date',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Fulfillment Status',
            'Subtotal',
            'Shipping Amount',
            'Cash Handling Fee',
            'Prepaid Savings',
            'Discount Amount',
            'Total Amount',
            'Shipping Address',
            'City',
            'State',
            'Pincode',
            'Country',
            'Items',
            'Terms Accepted',
            'Terms Accepted At',
        ];
    }

    protected function exportRow(Order $order): array
    {
        return [
            $order->id,
            $order->order_number,
            $order->created_at?->format('Y-m-d H:i:s'),
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->payment_method,
            $order->payment_status,
            $order->status,
            $order->fulfillment_status,
            $order->subtotal,
            $order->shipping_amount,
            $order->payment_fee_amount,
            $order->payment_discount_amount,
            $order->discount_amount,
            $order->total_amount,
            $order->shipping_address,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_pincode,
            $order->shipping_country,
            $order->items->map(function ($item) {
                $variant = collect([$item->variant?->color, $item->variant?->size])->filter()->implode(' / ');
                $suffix = $variant ? " ({$variant})" : '';

                return "{$item->name}{$suffix} x {$item->quantity}";
            })->implode('; '),
            $order->terms_accepted ? 'Yes' : 'No',
            $order->terms_accepted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
