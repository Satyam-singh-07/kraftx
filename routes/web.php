<?php

use App\Helpers\SeoHelper;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

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
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/term-and-condition', function () {
    $seo = [
        'title' => 'Terms & Conditions | ' . config('app.name', 'KraftX'),
        'description' => 'Read the terms and conditions for using the KraftX website, products, and services.',
        'canonical' => route('terms.conditions'),
        'type' => 'article',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Terms & Conditions | ' . config('app.name', 'KraftX'),
                'description' => 'Read the terms and conditions for using the KraftX website, products, and services.',
                'canonical' => route('terms.conditions'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Terms & Conditions', 'url' => route('terms.conditions')],
            ]),
            SeoHelper::faqSchema([
                [
                    'question' => 'Can KraftX update its terms and conditions?',
                    'answer' => 'Yes. KraftX may update the terms at any time, and continued use of the site means you accept the revised terms.',
                ],
                [
                    'question' => 'Are users allowed to use the website for unlawful activity?',
                    'answer' => 'No. The website and services may not be used for any illegal, abusive, or unauthorized purpose.',
                ],
            ]),
        ],
    ];

    return view('term-and-condition', compact('seo'));
})->name('terms.conditions');
Route::get('/privacy-policy', function () {
    $seo = [
        'title' => 'Privacy Policy | ' . config('app.name', 'KraftX'),
        'description' => 'Review how KraftX collects, uses, stores, and protects customer information.',
        'canonical' => route('privacy.policy'),
        'type' => 'article',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Privacy Policy | ' . config('app.name', 'KraftX'),
                'description' => 'Review how KraftX collects, uses, stores, and protects customer information.',
                'canonical' => route('privacy.policy'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Privacy Policy', 'url' => route('privacy.policy')],
            ]),
            SeoHelper::faqSchema([
                [
                    'question' => 'What customer information does KraftX collect?',
                    'answer' => 'KraftX may collect details such as name, address, email, and phone number for order processing, delivery, and support.',
                ],
                [
                    'question' => 'Does KraftX store payment card details?',
                    'answer' => 'No. Payment information is handled by trusted third-party gateways and is not stored by KraftX.',
                ],
            ]),
        ],
    ];

    return view('privacy-policy', compact('seo'));
})->name('privacy.policy');
Route::get('/return-and-refund', function () {
    $seo = [
        'title' => 'Return & Refund Policy | ' . config('app.name', 'KraftX'),
        'description' => 'Understand KraftX return, replacement, and refund rules for eligible orders and products.',
        'canonical' => route('return.refund'),
        'type' => 'article',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Return & Refund Policy | ' . config('app.name', 'KraftX'),
                'description' => 'Understand KraftX return, replacement, and refund rules for eligible orders and products.',
                'canonical' => route('return.refund'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Return & Refund Policy', 'url' => route('return.refund')],
            ]),
            SeoHelper::faqSchema([
                [
                    'question' => 'When should a return request be raised?',
                    'answer' => 'Return or refund issues should be reported within the policy window after delivery, along with proof such as an unboxing video where required.',
                ],
                [
                    'question' => 'Does KraftX refund the original payment method?',
                    'answer' => 'Refund and replacement eligibility depends on the policy and product condition. Refer to the return page for the exact rules that apply.',
                ],
            ]),
        ],
    ];

    return view('return-and-refund', compact('seo'));
})->name('return.refund');
Route::get('/shipping-policy', function () {
    $seo = [
        'title' => 'Shipping Policy | ' . config('app.name', 'KraftX'),
        'description' => 'Learn about KraftX shipping timelines, delivery expectations, and cancellation policy details.',
        'canonical' => route('shipping.policy'),
        'type' => 'article',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Shipping Policy | ' . config('app.name', 'KraftX'),
                'description' => 'Learn about KraftX shipping timelines, delivery expectations, and cancellation policy details.',
                'canonical' => route('shipping.policy'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Shipping Policy', 'url' => route('shipping.policy')],
            ]),
            SeoHelper::faqSchema([
                [
                    'question' => 'How long does KraftX take to process an order?',
                    'answer' => 'Orders are typically processed within one to two business days, though high-demand periods may require additional time.',
                ],
                [
                    'question' => 'Can I cancel an order after placing it?',
                    'answer' => 'Orders can usually be cancelled only within the allowed cancellation window and before they have been processed or shipped.',
                ],
            ]),
        ],
    ];

    return view('shipping-policy', compact('seo'));
})->name('shipping.policy');
Route::get('/contact-us', function () {
    $seo = [
        'title' => 'Contact Us | ' . config('app.name', 'KraftX'),
        'description' => 'Contact KraftX for order support, shipping help, and product-related questions.',
        'canonical' => route('contact.us'),
        'type' => 'website',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Contact Us | ' . config('app.name', 'KraftX'),
                'description' => 'Contact KraftX for order support, shipping help, and product-related questions.',
                'canonical' => route('contact.us'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Contact Us', 'url' => route('contact.us')],
            ]),
            array_merge(SeoHelper::organizationSchema(), [
                'contactPoint' => [[
                    '@type' => 'ContactPoint',
                    'telephone' => config('seo.support_phone'),
                    'contactType' => 'customer support',
                    'email' => config('seo.support_email'),
                    'areaServed' => config('seo.country_code', 'IN'),
                    'availableLanguage' => ['en', 'hi'],
                ]],
            ]),
        ],
    ];

    return view('contact-us', compact('seo'));
})->name('contact.us');
Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.us.store');
Route::post('/newsletter', [PublicNewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/track-order', function () {
    $seo = [
        'title' => 'Track Your Order | ' . config('app.name', 'KraftX'),
        'description' => 'Track your KraftX order status using your order ID and billing email address.',
        'canonical' => route('track.order'),
        'robots' => 'noindex,follow',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Track Your Order | ' . config('app.name', 'KraftX'),
                'description' => 'Track your KraftX order status using your order ID and billing email address.',
                'canonical' => route('track.order'),
            ]),
            SeoHelper::breadcrumbSchema([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Track Your Order', 'url' => route('track.order')],
            ]),
        ],
    ];

    return view('track-order', compact('seo'));
})->name('track.order');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product Management
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::delete('products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::resource('products', ProductController::class);
    Route::post('collections/{collection}/toggle-status', [CollectionController::class, 'toggleStatus'])->name('collections.toggle-status');
    Route::resource('collections', CollectionController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('reels', ReelController::class);
    Route::resource('deals', DealController::class);
    Route::resource('coupons', CouponController::class);
    Route::resource('tags', TagController::class);
    Route::resource('blog-categories', BlogCategoryController::class);
    Route::resource('blog-posts', BlogPostController::class);
    Route::get('blog-comments', [BlogCommentController::class, 'index'])->name('blog-comments.index');
    Route::patch('blog-comments/{comment}/status', [BlogCommentController::class, 'updateStatus'])->name('blog-comments.status');
    Route::delete('blog-comments/{comment}', [BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');
    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::patch('contact-messages/{message}/mark-read', [ContactMessageController::class, 'markRead'])->name('contact-messages.mark-read');
    Route::delete('contact-messages/{message}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::resource('newsletters', AdminNewsletterController::class)->only(['index', 'destroy']);

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('/settings', fn () => view('admin.dashboard', ['stats' => ['total_orders' => 0, 'total_revenue' => 0, 'total_products' => 0, 'total_customers' => 0, 'recent_orders' => [], 'sales_data' => ['labels' => [], 'data' => []], 'orders_data' => ['labels' => [], 'data' => []]]]))->name('settings.index');
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
    $seo = [
        'title' => 'Checkout | ' . config('app.name', 'KraftX'),
        'description' => 'Secure checkout for your KraftX order.',
        'canonical' => route('checkout'),
        'robots' => 'noindex,follow',
        'json_ld' => [
            SeoHelper::webPageSchema([
                'title' => 'Checkout | ' . config('app.name', 'KraftX'),
                'description' => 'Secure checkout for your KraftX order.',
                'canonical' => route('checkout'),
            ]),
        ],
    ];

    return view('checkout', compact('seo'));
})->name('checkout');

// Public Deals & Coupons
Route::get('/deals', [App\Http\Controllers\Public\DealController::class, 'index'])->name('deals.index');
Route::get('/deals/{slug}', [App\Http\Controllers\Public\DealController::class, 'show'])->name('deals.show');
Route::post('/coupon/apply', [App\Http\Controllers\Public\CouponController::class, 'apply'])->name('coupon.apply');

// Logout route (dummy for now)
Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
