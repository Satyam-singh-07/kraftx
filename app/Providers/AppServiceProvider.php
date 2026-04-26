<?php

namespace App\Providers;

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
        // Handle guest cart merging on login
        Event::listen(Login::class, function ($event) {
            $cartService = app(CartService::class);
            $cartService->mergeCarts($event->user);
        });
    }
}
