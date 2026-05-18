<pre><code class="language-php">// Top 10 most requested products
$topProducts = app(\App\Services\ProductDemandService::class)->topRequestedProducts(10);

// Products with pending demand
$pendingProducts = app(\App\Services\ProductDemandService::class)->productsWithPendingDemand();

// Users waiting for a product
$users = app(\App\Services\ProductDemandService::class)->usersWaitingForProduct($product);
</code></pre>
