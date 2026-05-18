@props(['product'])

@php
    $image = $product['image'] ?? '';
    $hoverImage = $product['hoverImage'] ?? $image;
    $imageSrc = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/']) ? $image : asset($image);
    $hoverSrc = \Illuminate\Support\Str::startsWith($hoverImage, ['http://', 'https://', '/']) ? $hoverImage : asset($hoverImage);
    $isInStock = $product['isInStock'] ?? (($product['stock'] ?? 1) > 0);
    $notifyRequested = (bool) ($product['notifyRequested'] ?? false);
    $notifyUrl = $product['notifyUrl'] ?? route('product.notify.store', $product['id']);
    $displayPrice = is_numeric($product['price'] ?? null) ? '₹' . number_format((float) $product['price'], 0) : ($product['price'] ?? '');
    $oldPrice = $product['oldPrice'] ?? null;
@endphp

<div class="card-product {{ ($product['hasSize'] ?? false) ? 'has-size' : '' }}">
    <div class="card-product_wrapper">
        <a href="{{ $product['url'] }}" class="product-img">
            <img class="img-product" loading="lazy" decoding="async" width="330" height="440"
                src="{{ $imageSrc }}" alt="{{ $product['name'] }} primary image">
            <img class="img-hover" loading="lazy" decoding="async" width="330" height="440"
                src="{{ $hoverSrc }}" alt="{{ $product['name'] }} alternate view">
        </a>
        <ul class="product-action_list">
            <li class="wishlist" data-product-id="{{ $product['id'] }}">
                <a href="#" class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-heart"></span>
                    <span class="tooltip">Add to Wishlist</span>
                </a>
            </li>
            {{-- <li class="compare">
                <a href="#compare" data-bs-toggle="offcanvas"
                    class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-ArrowsLeftRight"></span>
                    <span class="tooltip">Compare</span>
                </a>
            </li>
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas"
                    class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-Eye"></span>
                    <span class="tooltip">Quick view</span>
                </a>
            </li> --}}
        </ul>
        @if(isset($product['badges']))
            <ul class="product-badge_list">
                @foreach($product['badges'] as $badge)
                    <li class="product-badge_item text-caption-01 {{ $badge['type'] }}">{{ $badge['text'] }}</li>
                @endforeach
            </ul>
        @endif
        @if($product['hasSize'] ?? false)
            <div class="variant-box">
                <ul class="product-size_list">
                    @foreach($product['sizes'] as $size)
                        <li class="size-item text-caption-01">{{ $size }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="product-action_bot">
            @if($isInStock)
                <button type="button"
                    class="tf-btn btn-white small w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#quickAdd"
                    data-product-id="{{ $product['id'] }}">
                    Add To Cart
                </button>
            @else
                <form action="{{ $notifyUrl }}" method="POST" class="product-notify-form" data-product-id="{{ $product['id'] }}">
                    @csrf
                    <button type="submit" class="tf-btn btn-white small w-100 product-notify-button" {{ $notifyRequested ? 'disabled' : '' }}>
                        {{ $notifyRequested ? 'Notification Set' : 'Notify Me' }}
                    </button>
                    <p class="product-notify-message text-caption-01 text-white fw-bold mt-8 mb-0 {{ $notifyRequested ? '' : 'd-none' }}">
                        We will notify you when this product is back in stock
                    </p>
                </form>
            @endif
        </div>
        @if(isset($product['countdown']))
            <div class="product-countdown">
                <div class="js-countdown cd-has-zero" data-timer="{{ $product['countdown'] }}"
                    data-labels="D : ,H : ,M : ,S">
                </div>
            </div>
        @endif
    </div>
    <div class="card-product_info">
        <a href="{{ $product['url'] }}"
            class="name-product lh-24 fw-medium link-underline-text">
            {{ $product['name'] }}
        </a>
        <div class="star-wrap d-flex align-items-center">
            @for($i = 0; $i < 5; $i++)
                <i class="icon icon-Star"></i>
            @endfor
        </div>
        <div class="price-wrap">
            <span class="price-new text-primary fw-semibold">{{ $displayPrice }}</span>
            @if($oldPrice)
                <span class="price-old text-caption-01 cl-text-3">{{ $oldPrice }}</span>
            @endif
        </div>
        @if(isset($product['colors']))
            <ul class="product-color_list">
                @foreach($product['colors'] as $color)
                    <li class="product-color-item color-swatch hover-tooltip tooltip-bot {{ $loop->first ? 'active' : '' }}">
                        <span class="tooltip color-filter">{{ $color['name'] }}</span>
                        <span class="swatch-value {{ $color['class'] }}"></span>
                        <img src="{{ asset($color['image']) }}"
                            data-src="{{ asset($color['image']) }}" alt="{{ $product['name'] }} in {{ $color['name'] }}">
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
