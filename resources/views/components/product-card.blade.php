@props(['product'])

@php
    $image = $product['image'] ?? '';
    $hoverImage = $product['hoverImage'] ?? $image;
    $imageSrc = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/']) ? $image : asset($image);
    $hoverSrc = \Illuminate\Support\Str::startsWith($hoverImage, ['http://', 'https://', '/']) ? $hoverImage : asset($hoverImage);
@endphp

<div class="card-product {{ $product['hasSize'] ? 'has-size' : '' }}">
    <div class="card-product_wrapper">
        <a href="{{ $product['url'] }}" class="product-img">
            <img class="img-product" loading="lazy" decoding="async" width="330" height="440"
                src="{{ $imageSrc }}" alt="{{ $product['name'] }} primary image">
            <img class="img-hover" loading="lazy" decoding="async" width="330" height="440"
                src="{{ $hoverSrc }}" alt="{{ $product['name'] }} alternate view">
        </a>
        <ul class="product-action_list">
            <li class="wishlist">
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
        @if($product['hasSize'])
            <div class="variant-box">
                <ul class="product-size_list">
                    @foreach($product['sizes'] as $size)
                        <li class="size-item text-caption-01">{{ $size }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="product-action_bot">
            <button type="button" 
                class="tf-btn btn-white small w-100" 
                data-bs-toggle="modal" 
                data-bs-target="#quickAdd"
                data-product-id="{{ $product['id'] }}">
                Quick Add
            </button>
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
            <span class="price-new text-primary fw-semibold">{{ $product['price'] }}</span>
            @if(isset($product['oldPrice']))
                <span class="price-old text-caption-01 cl-text-3">{{ $product['oldPrice'] }}</span>
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
