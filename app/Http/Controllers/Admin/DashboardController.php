<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Sample data for the dashboard
        $stats = [
            'total_orders' => 1250,
            'total_revenue' => 45800.50,
            'total_products' => 142,
            'total_customers' => 850,
            'recent_orders' => [
                ['id' => '#ORD-1234', 'customer' => 'John Doe', 'amount' => 120.00, 'status' => 'Completed', 'date' => '2026-04-08'],
                ['id' => '#ORD-1235', 'customer' => 'Jane Smith', 'amount' => 85.50, 'status' => 'Pending', 'date' => '2026-04-08'],
                ['id' => '#ORD-1236', 'customer' => 'Robert Johnson', 'amount' => 210.00, 'status' => 'Shipped', 'date' => '2026-04-07'],
                ['id' => '#ORD-1237', 'customer' => 'Sarah Williams', 'amount' => 45.00, 'status' => 'Completed', 'date' => '2026-04-07'],
                ['id' => '#ORD-1238', 'customer' => 'Michael Brown', 'amount' => 135.20, 'status' => 'Cancelled', 'date' => '2026-04-06'],
            ],
            'sales_data' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [1200, 1900, 1500, 2500, 2200, 3000, 2800]
            ],
            'orders_data' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [12, 19, 15, 25, 22, 30, 28]
            ]
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function getStats()
    {
        // For AJAX requests
        return response()->json([
            'revenue' => 45800.50,
            'orders' => 1250,
            'customers' => 850,
            'products' => 142
        ]);
    }
}
