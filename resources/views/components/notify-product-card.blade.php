@props(['notifyRequest'])

@php
    $product = $notifyRequest->product;
    $primaryImage = $product?->images?->firstWhere('is_primary', true) ?? $product?->images?->first();
    $imagePath = $primaryImage?->image_path;
    $imageUrl = $imagePath
        ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/'])
            ? $imagePath
            : (str_starts_with($imagePath, 'assets/') ? asset($imagePath) : asset('storage/' . $imagePath)))
        : asset('assets/images/product/product-placeholder.jpg');
    $isAvailable = $product && $product->stock > 0;
    $price = $product ? ($product->sale_price ?? $product->price) : 0;
@endphp

<div class="notify-product-card">
    <a href="{{ $product ? route('product.show', $product->slug) : '#' }}" class="notify-product-card__image">
        <img src="{{ $imageUrl }}" alt="{{ $product?->name ?? 'Product' }}" loading="lazy">
    </a>
    <div class="notify-product-card__body">
        <div class="notify-product-card__top">
            <div>
                <a href="{{ $product ? route('product.show', $product->slug) : '#' }}" class="notify-product-card__name">
                    {{ $product?->name ?? 'Deleted product' }}
                </a>
                <p class="notify-product-card__meta">Requested {{ $notifyRequest->created_at->format('d M Y') }}</p>
            </div>
            <div class="notify-product-card__price">₹{{ number_format($price, 0) }}</div>
        </div>

        <div class="notify-product-card__badges">
            <span class="notify-badge {{ $isAvailable ? 'notify-badge--available' : 'notify-badge--unavailable' }}">
                {{ $isAvailable ? 'Available Now' : 'Unavailable' }}
            </span>
            <span class="notify-badge {{ $notifyRequest->is_notified ? 'notify-badge--notified' : 'notify-badge--pending' }}">
                {{ $notifyRequest->is_notified ? 'Notified' : 'Waiting' }}
            </span>
            @if($product)
                <span class="notify-badge notify-badge--stock">Stock: {{ $product->stock }}</span>
            @endif
        </div>

        <div class="notify-product-card__actions">
            @if($product)
                <a href="{{ route('product.show', $product->slug) }}" class="tf-btn small btn-stroke">View Product</a>
                @if($isAvailable)
                    <a href="{{ route('product.show', $product->slug) }}" class="tf-btn small animate-btn">Buy Now</a>
                @endif
            @endif
            <form method="POST" action="{{ route('account.notify-products.destroy', $notifyRequest) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="tf-btn small btn-stroke notify-product-card__remove">Remove</button>
            </form>
        </div>
    </div>
</div>
