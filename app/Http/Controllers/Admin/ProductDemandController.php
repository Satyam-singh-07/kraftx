<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductDemandService;
use Illuminate\Http\Request;

class ProductDemandController extends Controller
{
    public function __construct(private ProductDemandService $productDemandService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['product', 'date', 'notified', 'sort']);

        $products = $this->productDemandService
            ->demandQuery($filters)
            ->when(!empty($filters['product']), function ($query) use ($filters) {
                $query->where(function ($query) use ($filters) {
                    $query->where('name', 'like', '%' . $filters['product'] . '%')
                        ->orWhere('sku', 'like', '%' . $filters['product'] . '%');
                });
            })
            ->when(($filters['sort'] ?? 'demand') === 'latest', fn ($query) => $query->latest())
            ->when(($filters['sort'] ?? 'demand') !== 'latest', fn ($query) => $query->orderByDesc('notify_requests_count'))
            ->paginate(15)
            ->withQueryString();

        $stats = $this->productDemandService->dashboardStats();

        return view('admin.product-demands.index', compact('products', 'filters', 'stats'));
    }

    public function show(Request $request, Product $product)
    {
        $filters = $request->only(['date', 'notified']);
        $requests = $this->productDemandService
            ->requestQueryForProduct($product, $filters)
            ->paginate(25)
            ->withQueryString();

        return view('admin.product-demands.show', compact('product', 'requests', 'filters'));
    }
}
