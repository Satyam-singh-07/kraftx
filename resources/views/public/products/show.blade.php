<x-layout :seo="$seo">

    @php
        $colors = $product->variants->whereNotNull('color')->unique('color');
        $sizes = $product->variants->whereNotNull('size')->unique('size');
    @endphp

    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('assets/css/photoswipe.css') }}">
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
            .review-card-modern {
                border: 1px solid #ececec;
                border-radius: 18px;
                padding: 22px;
                background: #fff;
                box-shadow: 0 14px 38px rgba(17, 17, 17, 0.06);
            }
            .review-summary-card {
                border: 1px solid #ececec;
                border-radius: 18px;
                padding: 26px;
                background:
                    radial-gradient(120% 90% at 0% 0%, rgba(247, 186, 1, 0.16) 0%, rgba(255, 255, 255, 0) 60%),
                    linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
            }
            .rating-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #d9d9d9;
            }
            .rating-progress-row .progress {
                height: 7px;
                border-radius: 99px;
                background: #f3f3f3;
                overflow: hidden;
            }
            .rating-progress-row .progress-bar {
                background: linear-gradient(90deg, #f7ba01 0%, #f2a900 100%);
            }
            .review-form-card {
                border: 1px solid #ececec;
                border-radius: 18px;
                padding: 24px;
                background: #fff;
                box-shadow: 0 14px 38px rgba(17, 17, 17, 0.06);
            }
            .star-radio-wrap {
                display: flex;
                gap: 8px;
                flex-direction: row-reverse;
                justify-content: flex-end;
            }
            .star-radio-wrap input {
                display: none;
            }
            .star-radio-wrap label {
                cursor: pointer;
                color: #c9c9c9;
                font-size: 24px;
                transition: color 0.2s ease;
            }
            .star-radio-wrap label::before {
                content: "\2605";
            }
            .star-radio-wrap input:checked ~ label,
            .star-radio-wrap label:hover,
            .star-radio-wrap label:hover ~ label {
                color: #f7ba01;
            }
            .review-image-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(86px, 1fr));
                gap: 10px;
            }
            .review-image-grid img {
                width: 100%;
                height: 86px;
                object-fit: cover;
                border-radius: 10px;
                border: 1px solid #ebebeb;
            }
            .review-image-input {
                border: 1px dashed #d0d0d0;
                border-radius: 12px;
                padding: 12px;
                background: #fafafa;
            }
            .review-trigger-card {
                border: 1px solid #ececec;
                border-radius: 18px;
                padding: 20px;
                background: #fff;
                box-shadow: 0 14px 38px rgba(17, 17, 17, 0.06);
            }
            .review-modal .modal-content {
                border: 0;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 30px 80px rgba(17, 17, 17, 0.2);
            }
            .review-modal .modal-header {
                border-bottom: 1px solid #f0f0f0;
                padding: 20px 24px;
                background: linear-gradient(180deg, #ffffff 0%, #fcfcfc 100%);
            }
            .review-modal .modal-body {
                padding: 24px;
            }
        </style>
    </x-slot>

    <main id="wrapper">
       
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
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="icon icon-Star {{ $i <= round($averageRating) ? '' : 'cl-text-4' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-caption-01 cl-text-2">({{ $totalReviews }} reviews)</span>
                                        </div>
                                        <div class="br-line type-vertical"></div>
                                        <div class="meta_sold text-caption-01 d-flex align-items-center gap-4">
                                            <i class="icon icon-Lightning" style="color: #f7ba01;"></i>
                                            <span class="cl-text-2">{{ rand(51, 150) }} sold in last 48 hours</span>
                                        </div>
                                        <div class="br-line type-vertical"></div>
                                       
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

                                    @if($product->tags->isNotEmpty())
                                    <div class="product-infor-tags mb-20 d-flex flex-wrap gap-8">
                                        @foreach($product->tags->take(5) as $tag)
                                            <span class="text-caption-01 cl-text-2 bg-light px-10 py-4 rounded-pill border">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <div class="product-infor-highlights mb-20">
                                        <!-- Top List Section -->
                                        <div class="highlight-list-top mb-24">
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Truck" style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Free Shipping Available</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Lightning" style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Cash on Delivery available </span>
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
                                            <span id="viewing-count">{{ rand(101, 250) }}</span> people are viewing this item right now
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
                                        <button type="button" id="buy-now-btn" class="tf-btn type-xl btn-primary animate-btn w-100">
                                            Buy It Now
                                        </button>
                                    </div>
                                </div>

                                 <div class="tf-product-trust-seal">
                                    <p class="h6 text-seal">Guranteed Safe Checkout:</p>
                                    <ul class="list-card">
                                         <li class="card-item">
                                            <img width="50" height="32" src="{{ asset('assets/images/payment/upi.svg') }}"
                                                alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32" src="{{ asset('assets/images/payment/visa.svg') }}" alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32" src="{{ asset('assets/images/payment/master-card.svg') }}"
                                                alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32" src="{{ asset('assets/images/payment/amex.svg') }}" alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32" src="{{ asset('assets/images/payment/paypal.svg') }}"
                                                alt="card">
                                        </li>
                                       
                                       
                                    </ul>
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


        <div class="flat-spacing flat-animate-tab mt-80">
            <div class="container">
                <div class="text-center mb-40">
                    <h4 class="fw-medium">Customer Reviews</h4>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="review-summary-card h-100">
                            <p class="text-caption-01 cl-text-2 mb-8">Average Rating</p>
                            <div class="d-flex align-items-end gap-8 mb-8">
                                <p class="text-display fw-semibold mb-0">{{ number_format($averageRating, 1) }}</p>
                                <p class="text-caption-01 cl-text-2 mb-2">/ 5</p>
                            </div>
                            <div class="star-wrap normal d-flex align-items-center mb-8">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="icon icon-Star fs-20 {{ $i <= round($averageRating) ? '' : 'cl-text-4' }}"></i>
                                @endfor
                            </div>
                            <p class="cl-text-2 text-caption-01 mb-20">{{ $totalReviews }} {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</p>
                            @for($star = 5; $star >= 1; $star--)
                                <div class="rating-progress-row d-flex align-items-center gap-8 mb-10">
                                    <span class="text-caption-01 fw-medium" style="min-width: 16px;">{{ $star }}</span>
                                    <i class="icon icon-Star fs-14 cl-text-yellow"></i>
                                    <div class="progress flex-grow-1">
                                        <div class="progress-bar" style="width: {{ $ratingPercentages[$star] }}%"></div>
                                    </div>
                                    <span class="text-caption-01 cl-text-2" style="min-width: 40px;">{{ $ratingPercentages[$star] }}%</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="col-lg-8">
                        @if (session('success'))
                            <div class="alert alert-success mb-20">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mb-20">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mb-20">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="review-trigger-card mb-20">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <h5 class="mb-4">Share Your Experience</h5>
                                    @auth
                                        @if($hasPurchasedProduct)
                                            <p class="text-caption-01 cl-text-2 mb-0">Verified buyers can submit a rating and review.</p>
                                        @else
                                            <p class="text-caption-01 cl-text-2 mb-0">Only customers who purchased this product can review it.</p>
                                        @endif
                                    @else
                                        <p class="text-caption-01 cl-text-2 mb-0">Login with your purchase account to write a verified review.</p>
                                    @endauth
                                </div>
                                @auth
                                    @if($hasPurchasedProduct)
                                        <button type="button" class="tf-btn animate-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                            Write a Review
                                        </button>
                                    @else
                                        <button type="button" class="tf-btn animate-btn" disabled>
                                            Purchase Required
                                        </button>
                                    @endif
                                @else
                                    <a href="#sign" data-bs-toggle="modal" class="tf-btn animate-btn">
                                        Login to Review
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="mb-0">Latest Reviews</h6>
                            <p class="text-caption-01 cl-text-2 mb-0">{{ $totalReviews }} {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</p>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @forelse($reviews as $review)
                                <article class="review-card-modern">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-8">
                                        <div class="d-flex align-items-center gap-8">
                                            <h6 class="mb-0">{{ $review->name }}</h6>
                                            <span class="rating-dot"></span>
                                            <p class="text-caption-01 cl-text-2 mb-0">{{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="star-wrap d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star {{ $i <= $review->rating ? '' : 'cl-text-4' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-body-1 mb-0">{{ $review->comment }}</p>
                                    @if(!empty($review->images))
                                        <div class="review-image-grid mt-12">
                                            @foreach($review->images as $imagePath)
                                                <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" rel="noopener noreferrer">
                                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="Review image by {{ $review->name }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="review-card-modern text-center">
                                    <h6 class="mb-8">No reviews yet</h6>
                                    <p class="text-caption-01 cl-text-2 mb-0">Be the first customer to share your experience for this product.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            @if($hasPurchasedProduct)
                <div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('product.reviews.store', $product->slug) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-16">
                                        <label class="tf-lable fw-medium d-block mb-8">Your Rating</label>
                                        <div class="star-radio-wrap">
                                            @for($star = 5; $star >= 1; $star--)
                                                <input type="radio" id="modal-rating-{{ $star }}" name="rating" value="{{ $star }}" {{ (int) old('rating', 5) === $star ? 'checked' : '' }}>
                                                <label for="modal-rating-{{ $star }}" title="{{ $star }} stars"></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <fieldset class="tf-field">
                                                <label for="modal_review_name" class="tf-lable fw-medium">Your Name <span class="text-primary">*</span></label>
                                                <input type="text" id="modal_review_name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Your name" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6">
                                            <fieldset class="tf-field">
                                                <label for="modal_review_email" class="tf-lable fw-medium">Your Email <span class="text-primary">*</span></label>
                                                <input type="email" id="modal_review_email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="you@example.com" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-12">
                                            <fieldset class="tf-field d-flex flex-column">
                                                <label for="modal_review_comment" class="tf-lable fw-medium">Your Review <span class="text-primary">*</span></label>
                                                <textarea id="modal_review_comment" name="comment" placeholder="Write your review here" rows="5" required>{{ old('comment') }}</textarea>
                                            </fieldset>
                                        </div>
                                        <div class="col-12">
                                            <fieldset class="tf-field d-flex flex-column">
                                                <label for="modal_review_images" class="tf-lable fw-medium">Add Photos (Optional)</label>
                                                <div class="review-image-input">
                                                    <input type="file" id="modal_review_images" name="images[]" accept=".jpg,.jpeg,.png,.webp,image/*" multiple>
                                                    <p class="text-caption-01 cl-text-2 mt-8 mb-0">Upload up to 4 images (max 4MB each).</p>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <button type="submit" class="tf-btn animate-btn mt-16">Submit Review</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

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
                const buyNowBtn = document.getElementById('buy-now-btn');
                const stickyAddToCartBtn = document.getElementById('sticky-add-to-cart-btn');
                const reviewModalElement = document.getElementById('reviewModal');

                @if($errors->any() && auth()->check() && $hasPurchasedProduct)
                    if (reviewModalElement && typeof bootstrap !== 'undefined') {
                        const reviewModal = new bootstrap.Modal(reviewModalElement);
                        reviewModal.show();
                    }
                @endif
                
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
                        count = Math.max(100, Math.min(250, count));
                        viewingCountEl.textContent = count;
                    }, 2000);
                }
                
                function handleAddToCart(event) {
                    const isBuyNow = event.currentTarget.id === 'buy-now-btn';
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

                    if (isBuyNow) {
                        buyNowBtn.disabled = true;
                        buyNowBtn.textContent = 'Processing...';
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
                            if (isBuyNow) {
                                window.location.href = '/checkout';
                            } else {
                                // Open the drawer
                                const cartDrawerEl = document.getElementById('shoppingCart');
                                if (cartDrawerEl) {
                                    const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(cartDrawerEl);
                                    bsOffcanvas.show();
                                }
                            }
                        } else {
                            alert(data.message || 'Something went wrong. Please try again.');
                            if (isBuyNow) {
                                buyNowBtn.disabled = false;
                                buyNowBtn.textContent = 'Buy It Now';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        if (isBuyNow) {
                            buyNowBtn.disabled = false;
                            buyNowBtn.textContent = 'Buy It Now';
                        }
                    });
                }

                if (addToCartBtn) addToCartBtn.addEventListener('click', handleAddToCart);
                if (buyNowBtn) buyNowBtn.addEventListener('click', handleAddToCart);
                if (stickyAddToCartBtn) stickyAddToCartBtn.addEventListener('click', handleAddToCart);
            });
        </script>
        <script src="{{ asset('assets/js/plugin/drift.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugin/photoswipe.umd.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugin/photoswipe-lightbox.umd.min.js') }}"></script>
        <script src="{{ asset('assets/js/zoom.js') }}"></script>
    </x-slot>

</x-layout>
