<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
// BlogCategoryController

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DealController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsletterController as PublicNewsletterController;
use App\Http\Controllers\Public\QuickAddController;
use App\Http\Controllers\Public\SearchController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/term-and-condition', 'term-and-condition')->name('terms.conditions');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::view('/return-and-refund', 'return-and-refund')->name('return.refund');
Route::view('/shipping-policy', 'shipping-policy')->name('shipping.policy');
Route::view('/contact-us', 'contact-us')->name('contact.us');
Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.us.store');
Route::post('/newsletter', [PublicNewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/track-order', function () {
    return view('track-order');
})->name('track.order');

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
Route::get('/product/{slug}', [App\Http\Controllers\Public\ProductController::class, 'show'])->name('product.show');
Route::post('/product/{product:slug}/reviews', [App\Http\Controllers\Public\ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('product.reviews.store');
Route::get('/collection/{slug}', [App\Http\Controllers\Public\ProductController::class, 'collectionShow'])->name('collection.show');

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{post}/comment', [BlogController::class, 'storeComment'])->name('blog.comment.store');

// Quick Add Route
Route::get('/product/{product}/quick-add', [QuickAddController::class, 'show'])->name('product.quick-add.info');
Route::get('/cart/fetch', [CartController::class, 'fetch'])->name('cart.fetch');
Route::get('/cart/recommendations', [CartController::class, 'recommendations'])->name('cart.recommendations');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Search Routes
Route::get('/search', [SearchController::class, 'results'])->name('search.results');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');

Route::get('/checkout', function () {
    return 'Checkout Page Placeholder';
})->name('checkout');

// Public Deals & Coupons
Route::get('/deals', [App\Http\Controllers\Public\DealController::class, 'index'])->name('deals.index');
Route::get('/deals/{slug}', [App\Http\Controllers\Public\DealController::class, 'show'])->name('deals.show');
Route::post('/coupon/apply', [App\Http\Controllers\Public\CouponController::class, 'apply'])->name('coupon.apply');

// Logout route (dummy for now)
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
