<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductDemandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductNotifyRequestController extends Controller
{
    public function __construct(private ProductDemandService $productDemandService)
    {
    }

    public function store(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to request a back in stock notification.',
                    'login_required' => true,
                ], 401);
            }

            return redirect()
                ->guest(route('home'))
                ->with([
                    'auth_modal' => 'sign',
                    'auth_notice' => 'Please log in to request a back in stock notification.',
                ]);
        }

        $request->validate([
            'product_id' => ['nullable', 'integer'],
        ]);

        $this->productDemandService->createRequest($product, $request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'We will notify you when this product is back in stock',
            ]);
        }

        return back()->with('success', 'We will notify you when this product is back in stock');
    }
}
