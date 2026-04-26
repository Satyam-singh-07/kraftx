<x-layout>

    <x-slot name="seo">
        <script type="application/ld+json">
            {!! json_encode(\App\Helpers\SeoHelper::generateJsonLdProduct($product)) !!}
        </script>
        {!! \App\Helpers\SeoHelper::getSeoTags($product) !!}
    </x-slot>

    @php
        $colors = $product->variants->whereNotNull('color')->unique('color');
        $sizes = $product->variants->whereNotNull('size')->unique('size');
    @endphp

    @push('styles')
    <style>
        .custom-accordion .accordion-item {
            border: none;
            border-bottom: 1px solid #ebebeb !important;
            border-radius: 0 !important;
        }
        .custom-accordion .accordion-button {
            padding: 18px 0;
            font-weight: 700;
            font-size: 16px;
            color: #111;
            background-color: transparent !important;
            box-shadow: none !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-accordion .accordion-button::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: 18px;
            width: 18px;
            height: 18px;
            transition: transform 0.3s ease;
        }
        .custom-accordion .accordion-button:not(.collapsed)::after {
            transform: rotate(-180deg);
        }
        .custom-accordion .accordion-body {
            padding: 0 0 20px 0;
            color: #666;
            line-height: 1.6;
            font-size: 14px;
        }
        .custom-accordion .accordion-body strong {
            color: #333;
        }
    </style>
    @endpush

    <main id="wrapper">
        <!-- Breadcrumbs -->
        <div class="section-page-title-single flat-spacing-3">
            <div class="container">
                <div class="main-page-title">
                    <div class="breadcrumbs">
                        <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                        <i class="icon icon-CaretRightThin cl-text-3"></i>
                        <a href="#" class="text-caption-01 cl-text-3 link">Shop</a>
                        <i class="icon icon-CaretRightThin cl-text-3"></i>
                        <P class="text-caption-01">
                            {{ $product->name }}
                        </P>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Breadcrumbs -->

        <!-- Product Single -->
        <section class="section-product-single tf-main-product section-image-zoom pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="tf-product-media-wrap sticky-top">
                            <div class="product-thumbs-slider style-row row_left">
                                <div class="flat-wrap-media-product">
                                    <div dir="ltr" class="swiper tf-product-media-main" id="gallery-swiper-started"
                                        data-spacing="10">
                                        <div class="swiper-wrapper">
                                            @forelse($product->images as $image)
                                            <div class="swiper-slide">
                                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank"
                                                    class="item" data-pswp-width="576px" data-pswp-height="768px">
                                                    <img loading="lazy" width="576" height="768" class="tf-image-zoom"
                                                        data-zoom="{{ asset('storage/' . $image->image_path) }}"
                                                        src="{{ asset('storage/' . $image->image_path) }}"
                                                        alt="{{ $product->name }}">
                                                </a>
                                            </div>
                                            @empty
                                            <div class="swiper-slide">
                                                <a href="{{ asset('assets/images/product/product-placeholder.jpg') }}" target="_blank"
                                                    class="item" data-pswp-width="576px" data-pswp-height="768px">
                                                    <img loading="lazy" width="576" height="768" class="tf-image-zoom"
                                                        data-zoom="{{ asset('storage/' . $image->image_path) }}"
                                                        src="{{ asset('storage/' . $image->image_path) }}"
                                                        alt="{{ $product->name }}">
                                                </a>
                                            </div>
                                            @endforelse
                                        </div>
                                        <div class="swiper-button-next thumbs-next"></div>
                                        <div class="swiper-button-prev thumbs-prev"></div>
                                    </div>
                                </div>
                                <div dir="ltr" class="swiper tf-product-media-thumbs other-image-zoom"
                                    data-direction="vertical" data-preview="5" data-space="10">
                                    <div class="swiper-wrapper stagger-wrap">
                                        @foreach($product->images as $image)
                                        <div class="swiper-slide stagger-item">
                                            <div class="item">
                                                <img loading="lazy" width="82" height="110"
                                                    src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="tf-product-info-wrap position-relative mt-md-0" id="product-form-container">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="tf-zoom-main sticky-top"></div>
                            <div class="tf-product-info-list other-image-zoom">
                                <div class="tf-product-info-heading">
                                    <p class="product-infor-cate text-caption-01 mb-4">
                                        {{ $product->collections->first()->name ?? 'Collection' }}
                                    </p>
                                    <h3 class="product-infor-name mb-12">
                                        {{ $product->name }}
                                    </h3>
                                    <div class="product-infor-meta mb-20">
                                        <div class="meta_rate">
                                            <div class="star-wrap normal d-flex align-items-center">
                                                <i class="icon icon-Star"></i>
                                                <i class="icon icon-Star"></i>
                                                <i class="icon icon-Star"></i>
                                                <i class="icon icon-Star"></i>
                                                <i class="icon icon-Star"></i>
                                            </div>
                                            <span class="text-caption-01 cl-text-2">
                                                (0 reviews)
                                            </span>
                                        </div>
                                        <div class="br-line type-vertical"></div>
                                        <div class="meta_sold text-caption-01 d-flex align-items-center gap-4">
                                            <i class="icon icon-Lightning" style="color: #f7ba01;"></i>
                                            <span class="cl-text-2">{{ rand(51, 150) }} sold in last 48 hours</span>
                                        </div>
                                        <div class="br-line type-vertical"></div>
                                        <div class="meta_prd_code text-caption-01">
                                            <span class="cl-text-2">SKU:</span>
                                            <span>{{ $product->sku }}</span>
                                        </div>
                                    </div>
                                    <div class="product-infor-price mb-12"> 
                                        @if($product->sale_price)
                                            <h4 class="price-on-sale" data-price="{{ $product->sale_price }}">₹{{ number_format($product->sale_price, 0) }}</h4>
                                            <p class="cl-text-3 text-decoration-line-through">₹{{ number_format($product->price, 0) }}</p>
                                            <span class="badge-sale text-white fw-semibold text-caption-02">
                                                {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                            </span>
                                        @else
                                            <h4 class="price" data-price="{{ $product->price }}">₹{{ number_format($product->price, 0) }}</h4>
                                        @endif
                                    </div>
                                    
                                    <div class="product-infor-highlights mb-20">
                                        <!-- Top List Section -->
                                        <div class="highlight-list-top mb-24">
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Truck" style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Free Shipping Available</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Lightning" style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Cash on Delivery available (₹99 extra)</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-10">
                                                <i class="icon icon-Lightning" style="color: #2d4f1e; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #2d4f1e;">Get Upto ₹100 OFF on prepaid orders</span>
                                            </div>
                                        </div>

                                        <!-- Bottom Grid Section -->
                                        <div class="row g-2 text-center pt-20 border-top">
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-Truck2 d-block mb-8" style="font-size: 32px; color: #333;"></i>
                                                    <span class="text-caption-02 fw-medium text-dark d-block lh-sm">Fast Delivery</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-Star d-block mb-8" style="font-size: 32px; color: #333;"></i>
                                                    <span class="text-caption-02 fw-medium text-dark d-block lh-sm">Premium Quality</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-ArrowsLeftRight d-block mb-8" style="font-size: 32px; color: #333;"></i>
                                                    <span class="text-caption-02 fw-medium text-dark d-block lh-sm">Easy Replacement</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-ShieldCheck d-block mb-8" style="font-size: 32px; color: #333;"></i>
                                                    <span class="text-caption-02 fw-medium text-dark d-block lh-sm">Secure Payment</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-infor-view text-caption-01 d-flex align-items-center gap-4 mb-12">
                                        <i class="icon icon-Eye" style="color: #6c757d;"></i>
                                        <span class="cl-text-2 text-secondary">
                                            <span id="viewing-count">{{ rand(101, 500) }}</span> people are viewing this item right now
                                        </span>
                                    </div>
                                </div>
                                <div class="br-line"></div>
                                <div class="tf-product-variant">
                                    @if($colors->isNotEmpty())
                                    <div class="variant-picker-item variant-color">
                                        <div class="variant-picker-label">
                                            <div>
                                                Colors:
                                                <span class="variant-picker-label-value value-currentColor text-capitalize fw-medium">{{ $colors->first()->color }}</span>
                                            </div>
                                        </div>
                                        <div class="variant-picker-values">
                                            @foreach($colors as $variant)
                                            <div class="hover-tooltip tooltip-bot color-btn style-image {{ $loop->first ? 'active' : '' }}"
                                                data-color="{{ $variant->color }}">
                                                <div class="img">
                                                    <img loading="lazy" width="60" height="60"
                                                        src="{{ $variant->image_path ? asset('storage/' . $variant->image_path) : ($product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/product/product-placeholder.jpg')) }}"
                                                        alt="{{ $variant->color }}">
                                                </div>
                                                <span class="tooltip">{{ $variant->color }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($sizes->isNotEmpty())
                                    <div class="variant-picker-item variant-size">
                                        <div class="variant-picker-label">
                                            <div>
                                                Size:
                                                <span class="variant-picker-label-value value-currentSize text-capitalize fw-medium">{{ $sizes->first()->size }}</span>
                                            </div>
                                        </div>
                                        <div class="variant-picker-values">
                                            @foreach($sizes as $variant)
                                            <span class="size-btn {{ $loop->first ? 'active' : '' }}" data-size="{{ $variant->size }}">{{ $variant->size }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="tf-product-total-quantity" id="main-quantity-container" data-base-price="{{ $product->sale_price ?? $product->price }}">
                                        <p class="">Quantity:</p>
                                        <div class="group-action">
                                            <div class="wg-quantity">
                                                <button class="btn-quantity btn-decrease"><i class="icon icon-minus"></i></button>
                                                <input class="quantity-product" type="text" name="quantity" value="1">
                                                <button class="btn-quantity btn-increase"><i class="icon icon-plus"></i></button>
                                            </div>
                                            <button type="button" id="add-to-cart-btn" class="btn-action-price tf-btn type-xl animate-btn w-100">
                                                Add To Cart
                                                <span class="d-none d-sm-block d-md-none d-lg-block">&nbsp;-&nbsp;</span>
                                                <span class="price-add d-none d-sm-block d-md-none d-lg-block">₹{{ number_format($product->sale_price ?? $product->price, 0) }}</span>
                                            </button>
                                        </div>
                                        <a href="#" class="tf-btn type-xl btn-primary animate-btn w-100">
                                            Buy It Now
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Product Description Accordion -->
                                <div class="prd-desc-accordion custom-accordion mt-30">
                                    <div class="accordion" id="productAccordion">
                                        <!-- About the Product -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAbout">
                                                    About the Product
                                                </button>
                                            </h2>
                                            <div id="collapseAbout" class="accordion-collapse collapse show" data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    {!! $product->description !!}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Size & Weight -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSize">
                                                    Size & Weight
                                                </button>
                                            </h2>
                                            <div id="collapseSize" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                                <div class="accordion-body text-center">
                                                    @if($product->size_weight_image)
                                                        <img src="{{ asset('storage/' . $product->size_weight_image) }}" alt="Size & Weight" class="img-fluid rounded shadow-sm">
                                                    @else
                                                        <p>Size and weight information will be updated soon.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Perfect Placement -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePlacement">
                                                    Perfect Placement
                                                </button>
                                            </h2>
                                            <div id="collapsePlacement" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    @if($product->perfect_placement)
                                                        {!! $product->perfect_placement !!}
                                                    @else
                                                        <p>Information about perfect placement will be updated soon.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Premium Packaging -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePackaging">
                                                    Premium Packaging
                                                </button>
                                            </h2>
                                            <div id="collapsePackaging" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    <p>Every product is handled with care and packed in premium quality packaging materials to ensure it reaches you in perfect condition.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Return & Shipping -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturn">
                                                    Return & Shipping
                                                </button>
                                            </h2>
                                            <div id="collapseReturn" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    <p>We offer nationwide shipping across India. Delivery typically takes 3-7 business days. We accept returns within 7 days of delivery for damaged or defective products.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sticky ATC -->
        <div class="tf-sticky-btn-atc">
            <div class="container">
                <div class="tf-height-observer w-100 d-flex align-items-center">
                    <div class="tf-sticky-atc-product d-flex align-items-center">
                        <div class="atc-product-side">
                            <div class="prd_img">
                                <img loading="lazy" width="60" height="80"
                                    src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : '' }}" alt="{{ $product->name }}">
                            </div>
                            <div class="prd_info d-none d-lg-grid">
                                <p class="name__prd fw-medium lh-24">{{ $product->name }}</p>
                                <p class="distribute__prd text-caption-01 cl-text-3">
                                    {{ $colors->first()->color ?? '' }}{{ $sizes->isNotEmpty() ? ', ' . $sizes->first()->size : '' }}
                                </p>
                                <div class="d-flex align-items-center gap-10">
                                    <p class="price__prd fw-semibold">₹{{ number_format($product->sale_price ?? $product->price, 0) }}</p>
                                    @if($product->sale_price)
                                        <p class="cl-text-3 text-decoration-line-through text-caption-02">₹{{ number_format($product->price, 0) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tf-sticky-atc-infos" id="sticky-quantity-container" data-base-price="{{ $product->sale_price ?? $product->price }}">
                        <div class="d-flex align-items-center gap-10">
                            @if($sizes->isNotEmpty())
                            <div class="tf-sticky-atc-variant-price">
                                <p class="title">Size:</p>
                                <div class="tf-select style-2">
                                    <select id="sticky-size-select">
                                        @foreach($sizes as $variant)
                                        <option value="{{ $variant->size }}">{{ $variant->size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="tf-product-info-quantity">
                                <p class="title">Quantity:</p>
                                <div class="wg-quantity style-2">
                                    <button class="btn-quantity minus-btn"><i class="icon icon-minus"></i></button>
                                    <input class="quantity-product" type="text" name="quantity_sticky" value="1">
                                    <button class="btn-quantity plus-btn"><i class="icon icon-plus"></i></button>
                                </div>
                            </div>
                            <button type="button" id="sticky-add-to-cart-btn" class="tf-btn animate-btn btn-add-to-cart">
                                Add To Cart - ₹{{ number_format($product->sale_price ?? $product->price, 0) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Sticky ATC -->

        <!-- Related Product -->
        @if($relatedProducts->isNotEmpty())
        <div class="flat-spacing flat-animate-tab mt-80">
            <div class="container">
                <div class="text-center mb-40">
                    <h4 class="fw-medium">Related Products</h4>
                </div>
                <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3"
                    data-mobile-sm="2" data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10">
                    <div class="swiper-wrapper">
                        @foreach($relatedProducts as $related)
                        <div class="swiper-slide">
                            <x-product-card :product="[
                                'id' => $related->id,
                                'name' => $related->name,
                                'slug' => $related->slug,
                                'price' => $related->price,
                                'sale_price' => $related->sale_price,
                                'image' => $related->images->first() ? 'storage/' . $related->images->first()->image_path : '',
                                'url' => route('product.show', $related->slug),
                                'hasSize' => $related->variants->whereNotNull('size')->isNotEmpty(),
                                'colors' => $related->variants->whereNotNull('color')->unique('color')->map(fn($v) => ['name' => $v->color, 'image' => $related->images->first() ? 'storage/' . $related->images->first()->image_path : '']),
                                'badges' => []
                            ]" />
                        </div>
                        @endforeach
                    </div>
                    <div class="sw-line-default style-2 tf-sw-pagination"></div>
                </div>
            </div>
        </div>
        @endif
        <!-- /Related Product -->
    </main>

    <x-slot name="scripts">
        <script>
            function copyToClipboard() {
                var copyText = document.getElementById("coppyText").innerText;
                navigator.clipboard.writeText(copyText).then(function() {
                    alert("Copied to clipboard!");
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const addToCartBtn = document.getElementById('add-to-cart-btn');
                const stickyAddToCartBtn = document.getElementById('sticky-add-to-cart-btn');
                
                // Price Update Logic
                const mainQtyContainer = document.getElementById('main-quantity-container');
                const stickyQtyContainer = document.getElementById('sticky-quantity-container');
                const basePrice = parseFloat(mainQtyContainer?.dataset.basePrice || 0);
                
                function formatPrice(amount) {
                    return '₹' + Math.round(amount).toLocaleString('en-IN');
                }

                function updatePrice() {
                    const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 1;
                    const totalPrice = basePrice * quantity;
                    const priceDisplay = document.querySelector('#add-to-cart-btn .price-add');
                    if (priceDisplay) {
                        priceDisplay.textContent = formatPrice(totalPrice);
                    }
                }

                function updateStickyPrice() {
                    const quantity = parseInt(document.querySelector('input[name="quantity_sticky"]').value) || 1;
                    const totalPrice = basePrice * quantity;
                    if (stickyAddToCartBtn) {
                        stickyAddToCartBtn.textContent = `Add To Cart - ${formatPrice(totalPrice)}`;
                    }
                }

                // Main Quantity Listeners
                const mainDecrease = document.querySelector('#main-quantity-container .btn-decrease');
                const mainIncrease = document.querySelector('#main-quantity-container .btn-increase');
                const mainInput = document.querySelector('input[name="quantity"]');

                if (mainDecrease) {
                    mainDecrease.addEventListener('click', () => {
                        setTimeout(updatePrice, 10);
                    });
                }
                if (mainIncrease) {
                    mainIncrease.addEventListener('click', () => {
                        setTimeout(updatePrice, 10);
                    });
                }
                if (mainInput) {
                    mainInput.addEventListener('change', updatePrice);
                    mainInput.addEventListener('input', updatePrice);
                }

                // Sticky Quantity Listeners
                const stickyDecrease = document.querySelector('#sticky-quantity-container .minus-btn');
                const stickyIncrease = document.querySelector('#sticky-quantity-container .plus-btn');
                const stickyInput = document.querySelector('input[name="quantity_sticky"]');

                if (stickyDecrease) {
                    stickyDecrease.addEventListener('click', () => {
                        setTimeout(updateStickyPrice, 10);
                    });
                }
                if (stickyIncrease) {
                    stickyIncrease.addEventListener('click', () => {
                        setTimeout(updateStickyPrice, 10);
                    });
                }
                if (stickyInput) {
                    stickyInput.addEventListener('change', updateStickyPrice);
                    stickyInput.addEventListener('input', updateStickyPrice);
                }

                // Initial update
                updatePrice();
                updateStickyPrice();

                // Live viewing count update (realistic fluctuation)
                const viewingCountEl = document.getElementById('viewing-count');
                if (viewingCountEl) {
                    setInterval(() => {
                        let count = parseInt(viewingCountEl.textContent);
                        // 60% chance to increase, 40% chance to decrease
                        const change = Math.floor(Math.random() * 3) + 1;
                        if (Math.random() > 0.4 || count < 110) {
                            count += change;
                        } else {
                            count -= change;
                        }
                        // Cap between 100 and 500 for realism
                        count = Math.max(100, Math.min(500, count));
                        viewingCountEl.textContent = count;
                    }, 2000);
                }
                
                function handleAddToCart(event) {
                    const isSticky = event.currentTarget.id === 'sticky-add-to-cart-btn';
                    
                    const productId = document.querySelector('input[name="product_id"]').value;
                    const quantity = isSticky 
                        ? document.querySelector('input[name="quantity_sticky"]').value 
                        : document.querySelector('input[name="quantity"]').value;
                    
                    // Selected color
                    const activeColorBtn = document.querySelector('.variant-color .color-btn.active');
                    const color = activeColorBtn ? activeColorBtn.getAttribute('data-color') : null;
                    
                    // Selected size
                    let size = null;
                    if (isSticky) {
                        size = document.getElementById('sticky-size-select')?.value;
                    } else {
                        const activeSizeBtn = document.querySelector('.variant-size .size-btn.active');
                        size = activeSizeBtn ? activeSizeBtn.getAttribute('data-size') : null;
                    }

                    fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity,
                            color: color,
                            size: size
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Open the drawer
                            const cartDrawerEl = document.getElementById('shoppingCart');
                            if (cartDrawerEl) {
                                const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(cartDrawerEl);
                                bsOffcanvas.show();
                            }
                        } else {
                            alert(data.message || 'Something went wrong. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }

                if (addToCartBtn) addToCartBtn.addEventListener('click', handleAddToCart);
                if (stickyAddToCartBtn) stickyAddToCartBtn.addEventListener('click', handleAddToCart);
            });
        </script>
        <script src="{{ asset('assets/js/plugin/drift.min.js') }}"></script>
        <script src="{{ asset('assets/js/zoom.js') }}"></script>
    </x-slot>

</x-layout>
