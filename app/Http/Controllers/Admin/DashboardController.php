<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $dailyLabels = collect(range(6, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->format('D'));
        $dailyDates = collect(range(6, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->toDateString());

        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => (float) Order::whereIn('payment_status', ['paid', 'pending'])->sum('total_amount'),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'recent_orders' => Order::latest()->take(5)->get()->map(fn (Order $order) => [
                'id' => $order->order_number,
                'db_id' => $order->id,
                'customer' => $order->customer_name,
                'amount' => (float) $order->total_amount,
                'status' => ucfirst($order->status),
                'date' => $order->created_at->format('Y-m-d'),
            ]),
            'sales_data' => [
                'labels' => $dailyLabels,
                'data' => $dailyDates->map(fn ($date) => (float) Order::whereDate('created_at', $date)->sum('total_amount')),
            ],
            'orders_data' => [
                'labels' => $dailyLabels,
                'data' => $dailyDates->map(fn ($date) => Order::whereDate('created_at', $date)->count()),
            ],
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function getStats()
    {
        return response()->json([
            'revenue' => (float) Order::whereIn('payment_status', ['paid', 'pending'])->sum('total_amount'),
            'orders' => Order::count(),
            'customers' => User::where('role', 'customer')->count(),
            'products' => Product::count(),
        ]);
    }
}
