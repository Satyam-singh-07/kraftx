<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\Banner;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $banners = Banner::where('status', 1)
        ->where('placement', 'home_main')
        ->orderBy('sort_order')
        ->get();

    $collections = Collection::where('status', 1)
        ->orderBy('name')
        ->take(6)
        ->get();

    $topPicks = Product::with(['images', 'variants'])
        ->where('status', 1)
        ->orderByDesc('featured')
        ->orderByDesc('created_at')
        ->take(8)
        ->get()
        ->map(function (Product $product) {
            $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            $secondary = $product->images->first(function ($image) use ($primary) {
                return !$primary || $image->id !== $primary->id;
            });

            $fallbackImage = 'assets/images/product/product-1.jpg';
            $imageUrl = $primary ? Storage::url($primary->image_path) : $fallbackImage;
            $hoverUrl = $secondary ? Storage::url($secondary->image_path) : $imageUrl;

            $price = (float) ($product->price ?? 0);
            $salePrice = $product->sale_price !== null ? (float) $product->sale_price : null;
            $isOnSale = $salePrice !== null && $salePrice > 0 && $price > 0 && $salePrice < $price;

            $displayPrice = $isOnSale ? $salePrice : $price;
            $oldPrice = $isOnSale ? $price : null;

            $badges = [];
            if ($isOnSale) {
                $percent = (int) round(100 * (1 - ($salePrice / $price)));
                $badges[] = ['type' => 'sale', 'text' => '-' . $percent . '%'];
            }
            if ($product->created_at && $product->created_at->gt(now()->subDays(30))) {
                $badges[] = ['type' => 'new', 'text' => 'NEW'];
            }

            $sizes = $product->variants
                ->pluck('size')
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'name' => $product->name,
                'url' => $product->slug ? route('product.show', $product->slug) : route('product.detail'),
                'image' => $imageUrl,
                'hoverImage' => $hoverUrl,
                'price' => '$' . number_format($displayPrice, 2),
                'oldPrice' => $oldPrice !== null ? '$' . number_format($oldPrice, 2) : null,
                'hasSize' => !empty($sizes),
                'sizes' => $sizes,
                'badges' => !empty($badges) ? $badges : null,
            ];
        });

    return view('index', compact('banners', 'collections', 'topPicks'));
})->name('home');

Route::get('/product-detail', function () {
    return view('product-detail');
})->name('product.detail');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Product Management
    Route::post('products/bulk-delete', [\App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('collections', \App\Http\Controllers\Admin\CollectionController::class);
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::resource('reels', \App\Http\Controllers\Admin\ReelController::class);
    Route::resource('deals', \App\Http\Controllers\Admin\DealController::class);
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
    
    Route::get('/orders', fn() => view('admin.dashboard', ['stats' => ['total_orders' => 0, 'total_revenue' => 0, 'total_products' => 0, 'total_customers' => 0, 'recent_orders' => [], 'sales_data' => ['labels' => [], 'data' => []], 'orders_data' => ['labels' => [], 'data' => []]]]))->name('orders.index');
    Route::get('/settings', fn() => view('admin.dashboard', ['stats' => ['total_orders' => 0, 'total_revenue' => 0, 'total_products' => 0, 'total_customers' => 0, 'recent_orders' => [], 'sales_data' => ['labels' => [], 'data' => []], 'orders_data' => ['labels' => [], 'data' => []]]]))->name('settings.index');
});

// Public Product Routes
Route::get('/product/{slug}', [\App\Http\Controllers\Public\ProductController::class, 'show'])->name('product.show');
Route::get('/collection/{slug}', [\App\Http\Controllers\Public\ProductController::class, 'collectionShow'])->name('collection.show');

// Public Deals & Coupons
Route::get('/deals', [\App\Http\Controllers\Public\DealController::class, 'index'])->name('deals.index');
Route::get('/deals/{slug}', [\App\Http\Controllers\Public\DealController::class, 'show'])->name('deals.show');
Route::post('/coupon/apply', [\App\Http\Controllers\Public\CouponController::class, 'apply'])->name('coupon.apply');


// Logout route (dummy for now)
Route::post('/logout', function() {
    return redirect('/');
})->name('logout');
