<?php

use App\Http\Controllers\Api\ShiprocketCatalogController;
use App\Http\Controllers\Api\ShiprocketWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Shiprocket Catalog Sync Routes
Route::prefix('shiprocket')->group(function () {
    Route::get('/products', [ShiprocketCatalogController::class, 'fetchProducts']);
    Route::get('/products-by-collection', [ShiprocketCatalogController::class, 'fetchProductsByCollection']);
    Route::get('/collections', [ShiprocketCatalogController::class, 'fetchCollections']);
    
    // Webhook for Order Placement
    Route::post('/webhook/order', [ShiprocketWebhookController::class, 'handleOrder']);
});
