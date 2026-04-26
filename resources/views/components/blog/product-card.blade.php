@props(['product'])

<div class="blog-product-card my-40 p-20 border rounded-20 bg-white shadow-sm d-flex gap-24 align-items-center">
    <div class="blog-product-img flex-shrink-0" style="width: 150px; aspect-ratio: 1; border-radius: 12px; overflow: hidden;">
        @php
            $image = $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/product/product-placeholder.jpg');
        @endphp
        <img src="{{ $image }}" alt="{{ $product->name }}" class="w-100 h-100 object-cover">
    </div>
    <div class="blog-product-info flex-grow-1" style="
    margin-bottom: 33px;
">
        <h4 class="mb-8 font-semibold" style="font-size: 20px;">{{ $product->name }}</h4>
        <div class="blog-product-price mb-16">
            @if($product->sale_price)
                <span class="text-primary fw-bold fs-18">₹{{ number_format($product->sale_price, 0) }}</span>
                <span class="text-muted text-decoration-line-through ms-8">₹{{ number_format($product->price, 0) }}</span>
            @else
                <span class="text-dark fw-bold fs-18">₹{{ number_format($product->price, 0) }}</span>
            @endif
        </div>
        <div class="d-flex gap-12">
            <a href="{{ route('product.show', $product->slug) }}" class="tf-btn btn-outline animate-btn small">View Details</a>
            <button type="button" 
                class="tf-btn btn-fill animate-btn small btn-primary" 
                style="background: #000; color: #fff; border: none;"
                data-bs-toggle="modal" 
                data-bs-target="#quickAdd"
                data-product-id="{{ $product->id }}">
                Buy Now
            </button>
        </div>
    </div>
</div>

<style>
    .blog-product-card {
        transition: all 0.3s ease;
    }
    .blog-product-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    @media (max-width: 575px) {
        .blog-product-card {
            flex-direction: column;
            text-align: center;
        }
        .blog-product-img {
            width: 100% !important;
            max-width: 200px;
        }
        .blog-product-card .d-flex {
            justify-content: center;
        }
    }
</style>
