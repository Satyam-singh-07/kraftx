<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Collection;
use App\Observers\ProductObserver;
use App\Observers\CollectionObserver;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Shiprocket Catalog Sync Observers
        Product::observe(ProductObserver::class);
        Collection::observe(CollectionObserver::class);

        // Handle guest cart merging on login
        Event::listen(Login::class, function ($event) {
            $cartService = app(CartService::class);
            $cartService->mergeCarts($event->user);
        });
    }
}
