<x-layout :seo="$seo">

    @php
        $linkedOptions = $product->variants
            ->flatMap(function ($variant) {
                return $variant->linkedProducts()->map(function ($linkedProduct) use ($variant) {
                    return [
                        'id' => $variant->id,
                        'linked_product_id' => $linkedProduct->id,
                        'name' => $linkedProduct->name,
                        'sku' => $linkedProduct->sku,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'items_count' => (int) ($variant->items_count ?: 1),
                        'mrp' => (float) $linkedProduct->price,
                        'sale_price' => $linkedProduct->sale_price !== null ? (float) $linkedProduct->sale_price : null,
                        'discount_percent' =>
                            $linkedProduct->sale_price !== null &&
                            (float) $linkedProduct->price > (float) $linkedProduct->sale_price
                                ? (int) round(
                                    100 * (1 - (float) $linkedProduct->sale_price / (float) $linkedProduct->price),
                                )
                                : 0,
                        'price' => (float) ($linkedProduct->sale_price ?? $linkedProduct->price),
                        'stock' => (int) $linkedProduct->stock,
                        'images' => $linkedProduct->images
                            ->map(
                                fn($image) => [
                                    'thumb' => $image->thumb_url,
                                    'medium' => $image->medium_url,
                                    'zoom' => $image->zoom_url,
                                ],
                            )
                            ->values()
                            ->all(),
                    ];
                });
            })
            ->values();
        $hasVariants = $linkedOptions->isNotEmpty();
        $purchasableStock = max((int) $product->stock, (int) $linkedOptions->max('stock'));
        $variantPayload = $linkedOptions;
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

            .star-radio-wrap input:checked~label,
            .star-radio-wrap label:hover,
            .star-radio-wrap label:hover~label {
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

            .variant-picker-values .is-disabled {
                opacity: .35;
                cursor: not-allowed;
                pointer-events: none;
            }

            .variant-stock-message {
                margin: 10px 0 0;
                font-size: 13px;
                color: #666;
            }

            .variant-stock-message.is-out {
                color: #b42318;
            }
            .bulk-order-trigger {
                border: 1px solid #111 !important;
                color: #1f1f1f !important;
                background: #fff !important;
                font-weight: 700;
                text-decoration: none;
            }
            .bulk-order-trigger:hover,
            .bulk-order-trigger:focus,
            .bulk-order-trigger:active {
                border-color: #111 !important;
                color: #111 !important;
                background: #fff !important;
            }
            .bulk-order-modal .modal-dialog {
                width: calc(100% - 32px) !important;
                max-width: 720px !important;
                max-height: calc(100vh - 32px) !important;
            }
            .bulk-order-modal .modal-content {
                border: 0;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 18px 55px rgba(0, 0, 0, .18);
                max-height: calc(100vh - 32px) !important;
                display: flex;
                flex-direction: column;
            }
            .bulk-order-modal .modal-header {
                background: #111;
                color: #fff;
                border: 0;
                padding: 28px 32px;
            }
            .bulk-order-modal .modal-body {
                padding: 36px 40px 40px !important;
                overflow-y: auto;
                min-height: 0;
            }
            .bulk-order-modal .modal-body > p {
                line-height: 1.6;
                margin-bottom: 24px !important;
            }
            .bulk-order-modal .bulk-order-product {
                padding: 16px !important;
                border: 1px solid #ededed;
                background: #fafafa !important;
                margin: 0 0 32px !important;
            }
            .bulk-order-modal .bulk-order-product img {
                flex: 0 0 auto;
                border: 1px solid #e6e6e6;
            }
            .bulk-order-modal .form-control {
                min-height: 48px;
                border-radius: 8px;
                padding: 11px 13px;
                border-color: #dcdcdc;
            }
            .bulk-order-modal textarea.form-control {
                min-height: 120px;
                resize: vertical;
            }
            .bulk-order-modal .form-label {
                margin-bottom: 8px;
                color: #333;
                font-size: 13px;
                font-weight: 700;
            }
            .bulk-order-modal form .row {
                row-gap: 22px;
                margin-right: 0;
                margin-left: 0;
            }
            .bulk-order-modal form {
                margin: 0;
            }
            .bulk-order-modal .bulk-order-actions {
                margin-top: 10px;
                padding-top: 26px;
                border-top: 1px solid #ededed;
            }
            @media (max-width: 575.98px) {
                .bulk-order-modal .modal-header {
                    padding: 22px 18px;
                }
                .bulk-order-modal .modal-body {
                    padding: 24px 20px 28px !important;
                }
                .bulk-order-modal .modal-dialog,
                .bulk-order-modal .modal-content {
                    max-height: calc(100vh - 16px) !important;
                }
                .bulk-order-modal .bulk-order-actions {
                    flex-direction: column-reverse;
                }
            .bulk-order-modal .bulk-order-actions .tf-btn {
                    width: 100%;
                }
            }
            .bulk-order-submit {
                min-width: 210px;
            }
            .bulk-order-submit .bulk-order-loader {
                display: none;
                width: 16px;
                height: 16px;
                margin-right: 8px;
                border: 2px solid currentColor;
                border-right-color: transparent;
                border-radius: 50%;
                animation: bulk-order-spin .7s linear infinite;
                vertical-align: -3px;
            }
            .bulk-order-submit.is-loading .bulk-order-loader {
                display: inline-block;
            }
            .bulk-order-submit.is-loading {
                cursor: wait;
                opacity: .85;
            }
            @keyframes bulk-order-spin {
                to { transform: rotate(360deg); }
            }

            .product-option-list {
                display: grid;
                gap: 10px;
                grid-auto-flow: column;
                grid-auto-columns: minmax(116px, 132px);
                grid-template-columns: none;
                overflow-x: auto;
                padding: 2px 2px 10px;
                scrollbar-width: thin;
            }

            .product-option-heading {
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 12px;
            }

            .product-option-heading .option-hint {
                color: #777;
                font-size: 12px;
            }

            .product-option-btn {
                border: 1px solid #dedede;
                border-radius: 9px;
                background: #fff;
                padding: 6px;
                min-height: 178px;
                text-align: left;
                display: flex;
                flex-direction: column;
                gap: 5px;
                align-items: stretch;
                position: relative;
                transition: border-color .18s ease, box-shadow .18s ease, opacity .18s ease, transform .18s ease;
            }

            .product-option-btn.active {
                border: 2px solid #1677bd;
                padding: 5px;
                box-shadow: 0 0 0 1px rgba(22, 119, 189, .16);
                transform: translateY(-1px);
            }

            .product-option-btn.is-disabled {
                opacity: .45;
                cursor: not-allowed;
            }

            .product-option-thumb {
                width: 100%;
                height: 102px;
                border-radius: 6px;
                object-fit: cover;
                background: #f4f4f4;
                display: block;
            }

            .product-option-title {
                display: block;
                font-size: 11px;
                font-weight: 700;
                color: #111;
                line-height: 1.2;
                min-height: 26px;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .product-option-meta {
                display: block;
                margin-top: auto;
                color: #111;
                line-height: 1.2;
            }

            .product-option-price {
                font-size: 13px;
                font-weight: 700;
                color: #111;
            }

            .product-option-mrp {
                margin-left: 3px;
                color: #777;
                font-size: 10px;
                text-decoration: line-through;
            }

            .product-option-discount {
                display: block;
                color: #16833b;
                font-size: 10px;
                font-weight: 700;
                margin-top: 2px;
            }

            .product-option-check {
                position: absolute;
                top: 7px;
                right: 7px;
                width: 16px;
                height: 16px;
                border: 1px solid #d5d5d5;
                border-radius: 50%;
                background: rgba(255, 255, 255, .9);
            }

            .product-option-btn.active .product-option-check {
                border-color: #1677bd;
                background: #1677bd;
            }

            .product-option-btn.active .product-option-check::after {
                content: '';
                position: absolute;
                width: 5px;
                height: 8px;
                border-right: 2px solid #fff;
                border-bottom: 2px solid #fff;
                transform: rotate(45deg);
                left: 5px;
                top: 2px;
            }

            .product-option-meta {
                display: block;
                margin-top: 3px;
                font-size: 12px;
                color: #777;
                line-height: 1.3;
            }
        </style>
    </x-slot>

    <main id="wrapper">
        @if(session('bulk_order_success'))
            <div class="container pt-20">
                <div class="alert alert-success d-flex align-items-center gap-2 mb-0" role="alert">
                    <i class="icon icon-check-circle" aria-hidden="true"></i>
                    <span>{{ session('bulk_order_success') }}</span>
                </div>
            </div>
        @endif

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
                                                    <a href="{{ $image->zoom_url }}" target="_blank" class="item"
                                                        data-pswp-width="576px" data-pswp-height="768px">
                                                        <img loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                                            fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                                                            width="576" height="768" class="tf-image-zoom"
                                                            data-zoom="{{ $image->zoom_url }}"
                                                            src="{{ $image->medium_url }}"
                                                            srcset="{{ $image->thumb_url }} 400w, {{ $image->medium_url }} 900w, {{ $image->zoom_url }} 1600w"
                                                            sizes="(max-width: 767px) 100vw, 576px"
                                                            alt="{{ $product->name }}">
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="swiper-slide">
                                                    <a href="{{ asset('assets/images/product/product-placeholder.jpg') }}"
                                                        target="_blank" class="item" data-pswp-width="576px"
                                                        data-pswp-height="768px">
                                                        <img loading="eager" fetchpriority="high" width="576"
                                                            height="768" class="tf-image-zoom"
                                                            data-zoom="{{ asset('assets/images/product/product-placeholder.jpg') }}"
                                                            src="{{ asset('assets/images/product/product-placeholder.jpg') }}"
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
                                        @foreach ($product->images as $image)
                                            <div class="swiper-slide stagger-item">
                                                <div class="item">
                                                    <img loading="lazy" width="82" height="110"
                                                        src="{{ $image->thumb_url }}" alt="{{ $product->name }}">
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
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="icon icon-Star {{ $i <= round($averageRating) ? '' : 'cl-text-4' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-caption-01 cl-text-2">({{ $totalReviews }}
                                                reviews)</span>
                                        </div>
                                        <div class="br-line type-vertical"></div>
                                        <div class="meta_sold text-caption-01 d-flex align-items-center gap-4">
                                            <i class="icon icon-Lightning" style="color: #f7ba01;"></i>
                                            <span class="cl-text-2">{{ rand(51, 150) }} sold in last 48 hours</span>
                                        </div>
                                        <div class="br-line type-vertical"></div>

                                    </div>
                                    <div class="product-infor-price mb-12">
                                        @if ($product->sale_price)
                                            <h4 id="display-sale-price" class="price-on-sale"
                                                data-price="{{ $product->sale_price }}">
                                                ₹{{ number_format($product->sale_price, 0) }}</h4>
                                            <p id="display-mrp" class="cl-text-3 text-decoration-line-through">
                                                ₹{{ number_format($product->price, 0) }}</p>
                                            <span id="display-discount"
                                                class="badge-sale text-white fw-semibold text-caption-02">
                                                {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                                OFF
                                            </span>
                                        @else
                                            <h4 id="display-sale-price" class="price"
                                                data-price="{{ $product->price }}">
                                                ₹{{ number_format($product->price, 0) }}</h4>
                                            <p id="display-mrp" class="cl-text-3 text-decoration-line-through d-none">
                                            </p>
                                            <span id="display-discount"
                                                class="badge-sale text-white fw-semibold text-caption-02 d-none"></span>
                                        @endif
                                    </div>

                                    @if ($product->tags->isNotEmpty())
                                        <div class="product-infor-tags mb-20 d-flex flex-wrap gap-8">
                                            @foreach ($product->tags->take(5) as $tag)
                                                <span
                                                    class="text-caption-01 cl-text-2 bg-light px-10 py-4 rounded-pill border">#{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="product-infor-highlights mb-20">
                                        <!-- Top List Section -->
                                        <div class="highlight-list-top mb-24">
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Truck"
                                                    style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Free
                                                    Shipping Available</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-10 mb-8">
                                                <i class="icon icon-Lightning"
                                                    style="color: #b58b21; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #111;">Cash on
                                                    Delivery available </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-10">
                                                <i class="icon icon-Lightning"
                                                    style="color: #2d4f1e; font-size: 20px;"></i>
                                                <span class="text-caption-01 fw-medium" style="color: #2d4f1e;">Get
                                                    Upto ₹100 OFF on prepaid orders</span>
                                            </div>
                                        </div>

                                        <!-- Bottom Grid Section -->
                                        <div class="row g-2 text-center pt-20 border-top">
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-Truck2 d-block mb-8"
                                                        style="font-size: 32px; color: #333;"></i>
                                                    <span
                                                        class="text-caption-02 fw-medium text-dark d-block lh-sm">Fast
                                                        Delivery</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-Star d-block mb-8"
                                                        style="font-size: 32px; color: #333;"></i>
                                                    <span
                                                        class="text-caption-02 fw-medium text-dark d-block lh-sm">Premium
                                                        Quality</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-ArrowsLeftRight d-block mb-8"
                                                        style="font-size: 32px; color: #333;"></i>
                                                    <span
                                                        class="text-caption-02 fw-medium text-dark d-block lh-sm">Easy
                                                        Replacement</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="feature-item">
                                                    <i class="icon icon-ShieldCheck d-block mb-8"
                                                        style="font-size: 32px; color: #333;"></i>
                                                    <span
                                                        class="text-caption-02 fw-medium text-dark d-block lh-sm">Secure
                                                        Payment</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="product-infor-view text-caption-01 d-flex align-items-center gap-4 mb-12">
                                        <i class="icon icon-Eye" style="color: #6c757d;"></i>
                                        <span class="cl-text-2 text-secondary">
                                            <span id="viewing-count">{{ rand(101, 250) }}</span> people are viewing
                                            this item right now
                                        </span>
                                    </div>
                                </div>
                                <div class="br-line"></div>
                                <div class="tf-product-variant">
                                    @if ($hasVariants)
                                        <div class="variant-picker-item product-options">
                                            <div class="variant-picker-label">
                                                <div>
                                                    Choose an option
                                                    <span
                                                        class="variant-picker-label-value value-currentOption text-capitalize fw-medium">{{ $product->name }}</span>
                                                </div>
                                                <span class="option-hint">Select a product</span>
                                            </div>
                                            <div class="product-option-list">
                                                @php
                                                    $baseImage =
                                                        $product->images->first()?->thumb_url ?:
                                                        asset('assets/images/product/product-placeholder.jpg');
                                                @endphp
                                                <button type="button"
                                                    class="product-option-btn active {{ $product->stock <= 0 ? 'is-disabled' : '' }}"
                                                    data-option="standard" @disabled($product->stock <= 0)>
                                                    <img class="product-option-thumb" src="{{ $baseImage }}"
                                                        alt="{{ $product->name }}">
                                                    <span>
                                                        <span class="product-option-title">{{ $product->name }}</span>
                                                        <span class="product-option-meta">
                                                            <span
                                                                class="product-option-price">₹{{ number_format($product->sale_price ?? $product->price, 0) }}</span>
                                                            @if ($product->sale_price && $product->price > 0)
                                                                <span
                                                                    class="product-option-mrp">₹{{ number_format($product->price, 0) }}</span>
                                                                <span
                                                                    class="product-option-discount">{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                                                    OFF</span>
                                                            @endif
                                                        </span>
                                                    </span>
                                                    <span class="product-option-check" aria-hidden="true"></span>
                                                </button>
                                                @foreach ($linkedOptions as $option)
                                                    @php
                                                        $optionDetails = collect([$option['color'], $option['size']])
                                                            ->filter()
                                                            ->implode(' / ');
                                                        $optionDetails = $optionDetails
                                                            ? $optionDetails . ' · Pack of ' . $option['items_count']
                                                            : 'Pack of ' . $option['items_count'];
                                                        $optionImage = $option['images'][0]['thumb'] ?? $baseImage;
                                                    @endphp
                                                    <button type="button"
                                                        class="product-option-btn {{ $option['stock'] <= 0 ? 'is-disabled' : '' }}"
                                                        data-variant-id="{{ $option['id'] }}"
                                                        data-linked-product-id="{{ $option['linked_product_id'] }}"
                                                        @disabled($option['stock'] <= 0)>
                                                        <img class="product-option-thumb" src="{{ $optionImage }}"
                                                            alt="{{ $option['name'] }}">
                                                        <span>
                                                            <span
                                                                class="product-option-title">{{ $option['name'] }}</span>
                                                            <span class="product-option-meta">
                                                                <span
                                                                    class="product-option-price">₹{{ number_format($option['price'], 0) }}</span>
                                                                @if ($option['discount_percent'] > 0)
                                                                    <span
                                                                        class="product-option-mrp">₹{{ number_format($option['mrp'], 0) }}</span>
                                                                    <span
                                                                        class="product-option-discount">{{ $option['discount_percent'] }}%
                                                                        OFF</span>
                                                                @endif
                                                                <span
                                                                    class="d-block text-caption-02 cl-text-3">{{ $optionDetails }}</span>
                                                            </span>
                                                        </span>
                                                        <span class="product-option-check" aria-hidden="true"></span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="tf-product-total-quantity" id="main-quantity-container"
                                        data-base-price="{{ $product->sale_price ?? $product->price }}">
                                        @if ($purchasableStock > 0)
                                            <p class="">Quantity:</p>
                                            <div class="group-action">
                                                <div class="wg-quantity">
                                                    <button class="btn-quantity btn-decrease"><i
                                                            class="icon icon-minus"></i></button>
                                                    <input class="quantity-product" type="text" name="quantity"
                                                        value="1">
                                                    <button class="btn-quantity btn-increase"><i
                                                            class="icon icon-plus"></i></button>
                                                </div>
                                                <button type="button" id="add-to-cart-btn"
                                                    class="btn-action-price tf-btn type-xl animate-btn w-100">
                                                    Add To Cart
                                                    <span
                                                        class="d-none d-sm-block d-md-none d-lg-block">&nbsp;-&nbsp;</span>
                                                    <span
                                                        class="price-add d-none d-sm-block d-md-none d-lg-block">₹{{ number_format($product->sale_price ?? $product->price, 0) }}</span>
                                                </button>
                                            </div>
                                            <p id="variant-stock-message" class="variant-stock-message"></p>
                                            <button type="button" id="buy-now-btn"
                                                class="tf-btn type-xl btn-primary animate-btn w-100 mb-10">
                                                Buy It Now
                                            </button>
                                        @else
                                            <form action="{{ route('product.notify.store', $product) }}"
                                                method="POST" class="product-notify-form">
                                                @csrf
                                                <button type="submit"
                                                    class="tf-btn type-xl btn-primary animate-btn w-100 mb-10 product-notify-button"
                                                    {{ $hasNotifyRequest ? 'disabled' : '' }}>
                                                    {{ $hasNotifyRequest ? 'Notification Set' : 'Notify Me' }}
                                                </button>
                                                <p
                                                    class="product-notify-message text-body-2 text-white fw-bold mb-0 {{ $hasNotifyRequest ? '' : 'd-none' }}">
                                                    We will notify you when this product is back in stock
                                                </p>
                                            </form>
                                        @endif
                                        <button type="button" class="tf-btn type-xl bulk-order-trigger w-100 mt-10"
                                            data-bs-toggle="modal" data-bs-target="#bulkOrderModal">
                                            <i class="icon icon-shopping-cart-simple me-2"></i> Bulk Order
                                        </button>
                                    </div>
                                </div>

                                <div class="tf-product-trust-seal">
                                    <p class="h6 text-seal">Guranteed Safe Checkout:</p>
                                    <ul class="list-card">
                                        <li class="card-item">
                                            <img width="50" height="32"
                                                src="{{ asset('assets/images/payment/upi.svg') }}" alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32"
                                                src="{{ asset('assets/images/payment/visa.svg') }}" alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32"
                                                src="{{ asset('assets/images/payment/master-card.svg') }}"
                                                alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32"
                                                src="{{ asset('assets/images/payment/amex.svg') }}" alt="card">
                                        </li>
                                        <li class="card-item">
                                            <img width="50" height="32"
                                                src="{{ asset('assets/images/payment/paypal.svg') }}" alt="card">
                                        </li>


                                    </ul>
                                </div>

                                <!-- Product Description Accordion -->
                                <div class="prd-desc-accordion custom-accordion mt-30">
                                    <div class="accordion" id="productAccordion">
                                        <!-- About the Product -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseAbout">
                                                    About the Product
                                                </button>
                                            </h2>
                                            <div id="collapseAbout" class="accordion-collapse collapse show"
                                                data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    {!! $product->description !!}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Size & Weight -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseSize">
                                                    Size & Weight
                                                </button>
                                            </h2>
                                            <div id="collapseSize" class="accordion-collapse collapse"
                                                data-bs-parent="#productAccordion">
                                                <div class="accordion-body text-center">
                                                    @if ($product->size_weight_image)
                                                        <img src="{{ asset('storage/' . $product->size_weight_image) }}"
                                                            alt="Size & Weight" class="img-fluid rounded shadow-sm">
                                                    @else
                                                        <p>Size and weight information will be updated soon.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Perfect Placement -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapsePlacement">
                                                    Perfect Placement
                                                </button>
                                            </h2>
                                            <div id="collapsePlacement" class="accordion-collapse collapse"
                                                data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    @if ($product->perfect_placement)
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
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapsePackaging">
                                                    Premium Packaging
                                                </button>
                                            </h2>
                                            <div id="collapsePackaging" class="accordion-collapse collapse"
                                                data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    <p>Every product is handled with care and packed in premium quality
                                                        packaging materials to ensure it reaches you in perfect
                                                        condition.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Return & Shipping -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseReturn">
                                                    Return & Shipping
                                                </button>
                                            </h2>
                                            <div id="collapseReturn" class="accordion-collapse collapse"
                                                data-bs-parent="#productAccordion">
                                                <div class="accordion-body">
                                                    <p>We offer nationwide shipping across India. Delivery typically
                                                        takes 3-7 business days. We accept returns within 7 days of
                                                        delivery for damaged or defective products.</p>
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
                                    src="{{ $product->images->first() ? $product->images->first()->thumb_url : '' }}"
                                    alt="{{ $product->name }}">
                            </div>
                            <div class="prd_info d-none d-lg-grid">
                                <p class="name__prd fw-medium lh-24">{{ $product->name }}</p>
                                <p class="distribute__prd text-caption-01 cl-text-3">
                                    <span id="sticky-selected-variant">{{ $product->name }}</span>
                                </p>
                                <div class="d-flex align-items-center gap-10">
                                    <p id="sticky-sale-price" class="price__prd fw-semibold">
                                        ₹{{ number_format($product->sale_price ?? $product->price, 0) }}</p>
                                    <p id="sticky-mrp"
                                        class="cl-text-3 text-decoration-line-through text-caption-02 {{ $product->sale_price ? '' : 'd-none' }}">
                                        ₹{{ number_format($product->price, 0) }}</p>
                                    <span id="sticky-discount"
                                        class="badge-sale text-white fw-semibold text-caption-02 {{ $product->sale_price ? '' : 'd-none' }}">{{ $product->sale_price ? round((($product->price - $product->sale_price) / $product->price) * 100) . '%' : '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tf-sticky-atc-infos" id="sticky-quantity-container"
                        data-base-price="{{ $product->sale_price ?? $product->price }}">
                        <div class="d-flex align-items-center gap-10">
                            @if ($purchasableStock > 0)
                                <div class="tf-product-info-quantity">
                                    <p class="title">Quantity:</p>
                                    <div class="wg-quantity style-2">
                                        <button class="btn-quantity minus-btn"><i
                                                class="icon icon-minus"></i></button>
                                        <input class="quantity-product" type="text" name="quantity_sticky"
                                            value="1">
                                        <button class="btn-quantity plus-btn"><i class="icon icon-plus"></i></button>
                                    </div>
                                </div>
                                <button type="button" id="sticky-add-to-cart-btn"
                                    class="tf-btn animate-btn btn-add-to-cart">
                                    Add To Cart - ₹{{ number_format($product->sale_price ?? $product->price, 0) }}
                                </button>
                            @else
                                <form action="{{ route('product.notify.store', $product) }}" method="POST"
                                    class="product-notify-form w-100">
                                    @csrf
                                    <button type="submit"
                                        class="tf-btn animate-btn btn-add-to-cart w-100 product-notify-button"
                                        {{ $hasNotifyRequest ? 'disabled' : '' }}>
                                        {{ $hasNotifyRequest ? 'Notification Set' : 'Notify Me' }}
                                    </button>
                                </form>
                            @endif
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
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="icon icon-Star fs-20 {{ $i <= round($averageRating) ? '' : 'cl-text-4' }}"></i>
                                @endfor
                            </div>
                            <p class="cl-text-2 text-caption-01 mb-20">{{ $totalReviews }}
                                {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</p>
                            @for ($star = 5; $star >= 1; $star--)
                                <div class="rating-progress-row d-flex align-items-center gap-8 mb-10">
                                    <span class="text-caption-01 fw-medium"
                                        style="min-width: 16px;">{{ $star }}</span>
                                    <i class="icon icon-Star fs-14 cl-text-yellow"></i>
                                    <div class="progress flex-grow-1">
                                        <div class="progress-bar" style="width: {{ $ratingPercentages[$star] }}%">
                                        </div>
                                    </div>
                                    <span class="text-caption-01 cl-text-2"
                                        style="min-width: 40px;">{{ $ratingPercentages[$star] }}%</span>
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
                                        @if ($hasPurchasedProduct)
                                            <p class="text-caption-01 cl-text-2 mb-0">Verified buyers can submit a rating
                                                and review.</p>
                                        @else
                                            <p class="text-caption-01 cl-text-2 mb-0">Only customers who purchased this
                                                product can review it.</p>
                                        @endif
                                    @else
                                        <p class="text-caption-01 cl-text-2 mb-0">Verify your email with the purchase
                                            account to write a review.</p>
                                    @endauth
                                </div>
                                @auth
                                    @if ($hasPurchasedProduct)
                                        <button type="button" class="tf-btn animate-btn" data-bs-toggle="modal"
                                            data-bs-target="#reviewModal">
                                            Write a Review
                                        </button>
                                    @else
                                        <button type="button" class="tf-btn animate-btn" disabled>
                                            Purchase Required
                                        </button>
                                    @endif
                                @else
                                    <a href="#sign" data-bs-toggle="modal" class="tf-btn animate-btn">
                                        Verify Email to Review
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h6 class="mb-0">Latest Reviews</h6>
                            <p class="text-caption-01 cl-text-2 mb-0">{{ $totalReviews }}
                                {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</p>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @forelse($reviews as $review)
                                <article class="review-card-modern">
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-8">
                                        <div class="d-flex align-items-center gap-8">
                                            <h6 class="mb-0">{{ $review->name }}</h6>
                                            <span class="rating-dot"></span>
                                            <p class="text-caption-01 cl-text-2 mb-0">
                                                {{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="star-wrap d-flex align-items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="icon icon-Star {{ $i <= $review->rating ? '' : 'cl-text-4' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-body-1 mb-0">{{ $review->comment }}</p>
                                    @if (!empty($review->images))
                                        <div class="review-image-grid mt-12">
                                            @foreach ($review->images as $imagePath)
                                                <a href="{{ asset('storage/' . $imagePath) }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    <img src="{{ asset('storage/' . $imagePath) }}"
                                                        alt="Review image by {{ $review->name }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="review-card-modern text-center">
                                    <h6 class="mb-8">No reviews yet</h6>
                                    <p class="text-caption-01 cl-text-2 mb-0">Be the first customer to share your
                                        experience for this product.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            @if ($hasPurchasedProduct)
                <div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('product.reviews.store', $product->slug) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-16">
                                        <label class="tf-lable fw-medium d-block mb-8">Your Rating</label>
                                        <div class="star-radio-wrap">
                                            @for ($star = 5; $star >= 1; $star--)
                                                <input type="radio" id="modal-rating-{{ $star }}"
                                                    name="rating" value="{{ $star }}"
                                                    {{ (int) old('rating', 5) === $star ? 'checked' : '' }}>
                                                <label for="modal-rating-{{ $star }}"
                                                    title="{{ $star }} stars"></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <fieldset class="tf-field">
                                                <label for="modal_review_name" class="tf-lable fw-medium">Your Name <span
                                                        class="text-primary">*</span></label>
                                                <input type="text" id="modal_review_name" name="name"
                                                    value="{{ old('name', auth()->user()->name ?? '') }}"
                                                    placeholder="Your name" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6">
                                            <fieldset class="tf-field">
                                                <label for="modal_review_email" class="tf-lable fw-medium">Your Email
                                                    <span class="text-primary">*</span></label>
                                                <input type="email" id="modal_review_email" name="email"
                                                    value="{{ old('email', auth()->user()->email ?? '') }}"
                                                    placeholder="you@example.com" required>
                                            </fieldset>
                                        </div>
                                        <div class="col-12">
                                            <fieldset class="tf-field d-flex flex-column">
                                                <label for="modal_review_comment" class="tf-lable fw-medium">Your Review
                                                    <span class="text-primary">*</span></label>
                                                <textarea id="modal_review_comment" name="comment" placeholder="Write your review here" rows="5" required>{{ old('comment') }}</textarea>
                                            </fieldset>
                                        </div>
                                        <div class="col-12">
                                            <fieldset class="tf-field d-flex flex-column">
                                                <label for="modal_review_images" class="tf-lable fw-medium">Add Photos
                                                    (Optional)</label>
                                                <div class="review-image-input">
                                                    <input type="file" id="modal_review_images" name="images[]"
                                                        accept=".jpg,.jpeg,.png,.webp,image/*" multiple>
                                                    <p class="text-caption-01 cl-text-2 mt-8 mb-0">Upload up to 4 images
                                                        (max 4MB each).</p>
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
        @if ($relatedProducts->isNotEmpty())
            <div class="flat-spacing flat-animate-tab mt-80">
                <div class="container">
                    <div class="text-center mb-40">
                        <h4 class="fw-medium">Related Products</h4>
                    </div>
                    <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3"
                        data-mobile-sm="2" data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10">
                        <div class="swiper-wrapper">
                            @foreach ($relatedProducts as $related)
                                <div class="swiper-slide">
                                    <x-product-card :product="[
                                        'id' => $related->id,
                                        'name' => $related->name,
                                        'slug' => $related->slug,
                                        'price' => $related->price,
                                        'sale_price' => $related->sale_price,
                                        'image' => $related->images->first()
                                            ? $related->images->first()->thumb_url
                                            : '',
                                        'url' => route('product.show', $related->slug),
                                        'hasSize' => $related->variants->whereNotNull('size')->isNotEmpty(),
                                        'sizes' => $related->variants
                                            ->whereNotNull('size')
                                            ->unique('size')
                                            ->pluck('size')
                                            ->values()
                                            ->all(),
                                        'colors' => $related->variants
                                            ->whereNotNull('color')
                                            ->unique('color')
                                            ->map(
                                                fn($v) => [
                                                    'name' => $v->color,
                                                    'image' => $related->images->first()
                                                        ? $related->images->first()->thumb_url
                                                        : '',
                                                    'class' => '',
                                                ],
                                            ),
                                        'badges' => [],
                                        'stock' => (int) $related->stock,
                                        'isInStock' => $related->stock > 0,
                                        'notifyRequested' => auth()->check()
                                            ? auth()
                                                ->user()
                                                ->productNotifyRequests()
                                                ->where('product_id', $related->id)
                                                ->exists()
                                            : false,
                                        'notifyUrl' => route('product.notify.store', $related),
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

        <div class="modal fade bulk-order-modal" id="bulkOrderModal" tabindex="-1" aria-labelledby="bulkOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <p class="mb-1 text-caption-01 text-white-50">KraftX bulk orders</p>
                            <h5 class="modal-title text-white" id="bulkOrderModalLabel">Request a bulk order</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="cl-text-2 mb-20">
                            @if(session('bulk_order_otp'))
                                Enter the 6 digit code sent to <strong>{{ data_get(session('bulk_order_otp'), 'draft.email') }}</strong> to submit your enquiry.
                            @else
                                Tell us what you need and our team will contact you with availability and pricing.
                            @endif
                        </p>
                        <div class="bulk-order-product d-flex align-items-center gap-3 rounded-3 bg-light p-3 mb-20">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->thumb_url }}" width="58" height="70" class="rounded object-fit-cover" alt="{{ $product->name }}">
                            @endif
                            <div>
                                <p class="mb-1 fw-semibold">{{ $product->name }}</p>
                                <p class="mb-0 text-caption-01 cl-text-2">SKU: {{ $product->sku ?: 'N/A' }}</p>
                            </div>
                        </div>
                        @if(session('bulk_order_otp'))
                            <form action="{{ route('product.bulk-order.verify', $product) }}" method="POST" class="bulk-order-submit-form">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="bulk-otp" class="form-label">Email verification code *</label>
                                        <input id="bulk-otp" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="form-control" placeholder="Enter 6 digit code" required autofocus>
                                        @error('otp')
                                            <small class="mt-2 d-block text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="bulk-order-actions col-12 d-flex justify-content-end gap-2 pt-2">
                                        <button type="button" class="tf-btn btn-stroke" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="tf-btn btn-primary animate-btn bulk-order-submit">
                                            <span class="bulk-order-loader" aria-hidden="true"></span>
                                            <span class="bulk-order-submit-label">Verify &amp; submit enquiry</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form action="{{ route('product.bulk-order.resend-otp', $product) }}" method="POST" class="mt-3 text-center">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0 text-primary text-decoration-underline">Resend code</button>
                            </form>
                        @else
                        <form action="{{ route('product.bulk-order.store', $product) }}" method="POST" class="bulk-order-submit-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bulk-name" class="form-label">Name *</label>
                                    <input id="bulk-name" type="text" name="name" value="{{ old('name') }}" class="form-control" required maxlength="120">
                                </div>
                                <div class="col-md-6">
                                    <label for="bulk-phone" class="form-label">Phone number *</label>
                                    <input id="bulk-phone" type="tel" name="phone" value="{{ old('phone') }}" class="form-control" required maxlength="30">
                                </div>
                                <div class="col-md-6">
                                    <label for="bulk-email" class="form-label">Email address *</label>
                                    <input id="bulk-email" type="email" name="email" value="{{ old('email') }}" class="form-control" required maxlength="255">
                                </div>
                                <div class="col-md-6">
                                    <label for="bulk-quantity" class="form-label">Quantity required *</label>
                                    <input id="bulk-quantity" type="number" name="quantity" value="{{ old('quantity', 10) }}" min="2" max="1000000" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="bulk-message" class="form-label">Additional details <span class="text-muted">(optional)</span></label>
                                    <textarea id="bulk-message" name="message" rows="4" maxlength="2000" class="form-control" placeholder="Tell us about your delivery timeline, customization, or any other requirement.">{{ old('message') }}</textarea>
                                </div>
                                <div class="bulk-order-actions col-12 d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="tf-btn btn-stroke" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="tf-btn btn-primary animate-btn bulk-order-submit">
                                        <span class="bulk-order-loader" aria-hidden="true"></span>
                                        <span class="bulk-order-submit-label">Send verification code</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
                const bulkOrderModalElement = document.getElementById('bulkOrderModal');
                bulkOrderModalElement?.querySelectorAll('.bulk-order-submit-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        const bulkOrderSubmit = form.querySelector('.bulk-order-submit');
                        if (bulkOrderSubmit?.classList.contains('is-loading')) {
                            event.preventDefault();
                            return;
                        }

                        bulkOrderSubmit?.classList.add('is-loading');
                        bulkOrderSubmit?.setAttribute('disabled', 'disabled');
                        bulkOrderSubmit?.setAttribute('aria-busy', 'true');
                        const submitLabel = bulkOrderSubmit?.querySelector('.bulk-order-submit-label');
                        if (submitLabel) submitLabel.textContent = 'Please wait...';
                    });
                });

                @if ($errors->any() && auth()->check() && $hasPurchasedProduct)
                    if (reviewModalElement && typeof bootstrap !== 'undefined') {
                        const reviewModal = new bootstrap.Modal(reviewModalElement);
                        reviewModal.show();
                    }
                @endif

                @if(session('bulk_order_otp') || old('quantity') && ($errors->has('quantity') || $errors->has('phone') || $errors->has('email') || $errors->has('name') || $errors->has('message') || $errors->has('otp')))
                    if (bulkOrderModalElement && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(bulkOrderModalElement).show();
                    }
                @endif

                // Price Update Logic
                const mainQtyContainer = document.getElementById('main-quantity-container');
                const stickyQtyContainer = document.getElementById('sticky-quantity-container');
                const variants = @json($variantPayload);
                const hasVariants = variants.length > 0;
                const basePrice = parseFloat(mainQtyContainer?.dataset.basePrice || 0);
                const baseMrp = @json((float) $product->price);
                const baseSalePrice = @json($product->sale_price !== null ? (float) $product->sale_price : null);
                const mainSwiperEl = document.querySelector('.tf-product-media-main');
                const thumbSwiperEl = document.querySelector('.tf-product-media-thumbs');
                const mainWrapper = mainSwiperEl?.querySelector('.swiper-wrapper');
                const thumbWrapper = thumbSwiperEl?.querySelector('.swiper-wrapper');
                let mainSlides = Array.from(document.querySelectorAll('.tf-product-media-main .swiper-slide'));
                let thumbSlides = Array.from(document.querySelectorAll('.tf-product-media-thumbs .swiper-slide'));
                const stickyProductImage = document.querySelector('.tf-sticky-atc-product .prd_img img');
                const originalGallery = mainSlides.map((slide, index) => {
                    const link = slide.querySelector('a.item');
                    const image = link?.querySelector('img');
                    const thumb = thumbSlides[index]?.querySelector('img');

                    return {
                        slide,
                        link,
                        image,
                        thumb,
                        src: image?.getAttribute('src'),
                        srcset: image?.getAttribute('srcset'),
                        zoom: image?.getAttribute('data-zoom'),
                        href: link?.getAttribute('href'),
                        thumbSrc: thumb?.getAttribute('src'),
                        display: slide.style.display || '',
                        thumbDisplay: thumbSlides[index]?.style.display || '',
                    };
                });
                const originalStickyImage = stickyProductImage?.getAttribute('src');
                let selectedVariantId = null;
                let selectedLinkedProductId = null;
                const productName = @json($product->name);
                const baseOptionLabel = @json($product->name);

                function formatPrice(amount) {
                    return '₹' + Math.round(amount).toLocaleString('en-IN');
                }

                function currentVariant() {
                    if (!selectedVariantId) return null;
                    return variants.find(variant =>
                        Number(variant.id) === Number(selectedVariantId) &&
                        Number(variant.linked_product_id) === Number(selectedLinkedProductId)
                    ) || null;
                }

                function currentUnitPrice() {
                    return Number(currentVariant()?.price ?? basePrice);
                }

                function syncDisplayedPricing(variant) {
                    const mrp = Number(variant?.mrp ?? baseMrp);
                    const salePrice = variant ?
                        (variant.sale_price === null || variant.sale_price === undefined ? null : Number(variant
                            .sale_price)) :
                        baseSalePrice;
                    const hasDiscount = salePrice !== null && salePrice < mrp;
                    const displayPrice = hasDiscount ? salePrice : mrp;
                    const salePriceElement = document.getElementById('display-sale-price');
                    const mrpElement = document.getElementById('display-mrp');
                    const discountElement = document.getElementById('display-discount');
                    const stickyPriceElement = document.getElementById('sticky-sale-price');
                    const stickyMrpElement = document.getElementById('sticky-mrp');
                    const stickyDiscountElement = document.getElementById('sticky-discount');

                    if (salePriceElement) {
                        salePriceElement.textContent = formatPrice(displayPrice);
                        salePriceElement.classList.toggle('price-on-sale', hasDiscount);
                        salePriceElement.classList.toggle('price', !hasDiscount);
                    }
                    [mrpElement, stickyMrpElement].forEach(element => {
                        if (!element) return;
                        element.textContent = formatPrice(mrp);
                        element.classList.toggle('d-none', !hasDiscount);
                    });
                    [discountElement, stickyDiscountElement].forEach(element => {
                        if (!element) return;
                        const discount = hasDiscount ?
                            Math.max(0, Math.round(100 * (1 - (displayPrice / mrp)))) :
                            0;
                        element.textContent = discount ? discount + '% OFF' : '';
                        element.classList.toggle('d-none', !discount);
                    });
                    if (stickyPriceElement) stickyPriceElement.textContent = formatPrice(displayPrice);
                }

                function selectedVariantText() {
                    const variant = currentVariant();
                    if (!variant) return baseOptionLabel;

                    return variant.name || [variant.color, variant.size].filter(Boolean).join(' / ') || 'Option';
                }

                function createThumbSlide() {
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide stagger-item';
                    const item = document.createElement('div');
                    item.className = 'item';
                    const image = document.createElement('img');
                    image.loading = 'lazy';
                    image.width = 82;
                    image.height = 110;
                    image.alt = productName;
                    item.appendChild(image);
                    slide.appendChild(item);
                    return slide;
                }

                function ensureGallerySlots(count) {
                    if (!mainWrapper || mainSlides.length === 0) return;

                    while (mainSlides.length < count) {
                        const clonedMain = mainSlides[mainSlides.length - 1].cloneNode(true);
                        clonedMain.style.display = '';
                        mainWrapper.appendChild(clonedMain);
                        mainSlides = Array.from(mainWrapper.querySelectorAll('.swiper-slide'));
                    }

                    if (!thumbWrapper) return;

                    while (thumbSlides.length < count) {
                        const clonedThumb = thumbSlides.length > 0 ?
                            thumbSlides[thumbSlides.length - 1].cloneNode(true) :
                            createThumbSlide();
                        clonedThumb.style.display = '';
                        thumbWrapper.appendChild(clonedThumb);
                        thumbSlides = Array.from(thumbWrapper.querySelectorAll('.swiper-slide'));
                    }
                }

                function applyGalleryImages(variant) {
                    const images = variant?.images || [];
                    const usingVariantImages = images.length > 0;
                    ensureGallerySlots(Math.max(originalGallery.length, images.length));

                    mainSlides.forEach((slide, index) => {
                        const entry = originalGallery[index];
                        const replacement = images[index];
                        const visible = usingVariantImages ? Boolean(replacement) : Boolean(entry);
                        const link = slide.querySelector('a.item');
                        const image = link?.querySelector('img');
                        const thumb = thumbSlides[index]?.querySelector('img');

                        slide.style.display = visible ? (entry?.display || '') : 'none';
                        if (thumbSlides[index]) thumbSlides[index].style.display = visible ? (entry
                            ?.thumbDisplay || '') : 'none';

                        if (!image) return;

                        const src = replacement?.medium || entry?.src;
                        const zoom = replacement?.zoom || entry?.zoom || src;
                        const thumbSrc = replacement?.thumb || entry?.thumbSrc || src;
                        if (!src) return;

                        image.src = src;
                        image.srcset = replacement ?
                            `${replacement.thumb} 400w, ${replacement.medium} 900w, ${replacement.zoom} 1600w` :
                            (entry?.srcset || src);
                        image.dataset.zoom = zoom;
                        if (link) link.href = zoom;
                        if (thumb) thumb.src = thumbSrc;
                    });

                    if (stickyProductImage) {
                        stickyProductImage.src = images[0]?.thumb || originalStickyImage || stickyProductImage.src;
                    }

                    [mainSwiperEl, thumbSwiperEl].filter(Boolean).forEach(element => {
                        if (element.swiper) element.swiper.update();
                    });
                }

                function syncVariantControls() {
                    const optionLabel = document.querySelector('.value-currentOption');
                    const stickyVariant = document.getElementById('sticky-selected-variant');
                    const stockMessage = document.getElementById('variant-stock-message');
                    const selected = currentVariant();
                    const stock = selected ? Number(selected.stock || 0) : {{ (int) $product->stock }};
                    const mainQty = document.querySelector('input[name="quantity"]');
                    const stickyQty = document.querySelector('input[name="quantity_sticky"]');
                    [mainQty, stickyQty].forEach(input => {
                        if (!input || stock <= 0) return;
                        input.value = Math.max(1, Math.min(parseInt(input.value) || 1, stock));
                    });

                    document.querySelectorAll('.product-option-btn').forEach(button => {
                        const isStandard = button.dataset.option === 'standard';
                        const isActive = isStandard ?
                            selectedVariantId === null :
                            Number(button.dataset.variantId) === Number(selectedVariantId) &&
                            Number(button.dataset.linkedProductId) === Number(selectedLinkedProductId);
                        button.classList.toggle('active', isActive);
                    });

                    if (optionLabel) optionLabel.textContent = selectedVariantText();
                    if (stickyVariant) stickyVariant.textContent = selectedVariantText();
                    applyGalleryImages(selected);
                    syncDisplayedPricing(selected);
                    if (stockMessage) {
                        stockMessage.textContent = stock > 0 ? 'Available' : 'This selection is currently out of stock';
                        stockMessage.classList.toggle('is-out', stock <= 0);
                    }

                    [addToCartBtn, buyNowBtn, stickyAddToCartBtn].forEach(button => {
                        if (!button) return;
                        button.disabled = stock <= 0;
                    });

                    updatePrice();
                    updateStickyPrice();
                }

                function updatePrice() {
                    const quantityInput = document.querySelector('input[name="quantity"]');
                    if (!quantityInput) return;
                    const quantity = parseInt(quantityInput.value) || 1;
                    const totalPrice = currentUnitPrice() * quantity;
                    const priceDisplay = document.querySelector('#add-to-cart-btn .price-add');
                    if (priceDisplay) {
                        priceDisplay.textContent = formatPrice(totalPrice);
                    }
                }

                function updateStickyPrice() {
                    const quantityInput = document.querySelector('input[name="quantity_sticky"]');
                    if (!quantityInput) return;
                    const quantity = parseInt(quantityInput.value) || 1;
                    const totalPrice = currentUnitPrice() * quantity;
                    if (stickyAddToCartBtn) {
                        stickyAddToCartBtn.textContent = `Add To Cart - ${formatPrice(totalPrice)}`;
                    }
                }

                document.querySelectorAll('.product-option-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        if (button.classList.contains('is-disabled')) return;
                        selectedVariantId = button.dataset.option === 'standard' ? null : button.dataset
                            .variantId;
                        selectedLinkedProductId = button.dataset.option === 'standard' ? null : button
                            .dataset.linkedProductId;
                        syncVariantControls();
                    });
                });

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
                syncVariantControls();

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
                    const quantity = isSticky ?
                        document.querySelector('input[name="quantity_sticky"]').value :
                        document.querySelector('input[name="quantity"]').value;

                    const variant = currentVariant();
                    const availableStock = variant ? Number(variant.stock || 0) : {{ (int) $product->stock }};
                    if (availableStock <= 0) {
                        alert('This selection is currently out of stock.');
                        return;
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
                                variant_id: variant?.id || null,
                                linked_product_id: variant?.linked_product_id || null,
                                color: variant?.color || null,
                                size: variant?.size || null
                            })
                        })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                                throw new Error(firstError || data.message ||
                                    'Something went wrong. Please try again.');
                            }
                            return data;
                        })
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
                            alert(error.message || 'An error occurred. Please try again.');
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
