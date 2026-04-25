<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\Banner;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Public\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/product-detail', function () {
    return view('product-detail');
})->name('product.detail');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Product Management
    Route::post('products/bulk-delete', [\App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::delete('products/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('collections/{collection}/toggle-status', [\App\Http\Controllers\Admin\CollectionController::class, 'toggleStatus'])->name('collections.toggle-status');
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

// Cart Routes
Route::post('/cart/add', [\App\Http\Controllers\Public\CartController::class, 'add'])->name('cart.add');

// Public Deals & Coupons
Route::get('/deals', [\App\Http\Controllers\Public\DealController::class, 'index'])->name('deals.index');
Route::get('/deals/{slug}', [\App\Http\Controllers\Public\DealController::class, 'show'])->name('deals.show');
Route::post('/coupon/apply', [\App\Http\Controllers\Public\CouponController::class, 'apply'])->name('coupon.apply');


// Logout route (dummy for now)
Route::post('/logout', function() {
    return redirect('/');
})->name('logout');
