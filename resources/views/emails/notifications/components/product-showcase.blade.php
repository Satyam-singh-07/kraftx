<div class="kx-product">
    <div class="kx-product-image-wrap">
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="kx-product-image">
    </div>
    <div class="kx-product-panel">
        <p class="kx-product-label">Available now</p>
        <h2>{{ $product->name }}</h2>
        <p class="kx-product-price">₹{{ number_format($price, 0) }}</p>
        <p class="kx-product-note">Your notify request has been fulfilled. We have reserved no inventory, so checkout early for the best chance of purchase.</p>
    </div>
</div>
