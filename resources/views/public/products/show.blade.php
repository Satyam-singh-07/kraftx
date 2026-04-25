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

    <main id="wrapper">

        <!-- Product Single -->
        <section class="section-product-single tf-main-product section-image-zoom">
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
                                    </div>
                                    <div class="product-infor-price mb-12">
                                        @if($product->sale_price)
                                            <h4 class="price-on-sale">₹{{ number_format($product->sale_price, 2) }}</h4>
                                            <div class="br-line type-vertical"></div>
                                            <p class="cl-text-3 text-decoration-line-through">₹{{ number_format($product->price, 2) }}</p>
                                            <span class="badge-sale text-white fw-semibold text-caption-02">
                                                -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                            </span>
                                        @else
                                            <h4 class="price">₹{{ number_format($product->price, 2) }}</h4>
                                        @endif
                                    </div>
                                    <p class="product-infor-desc cl-text-2 mb-12">
                                        {{ $product->short_description }}
                                    </p>
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

                                    <div class="tf-product-total-quantity">
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
                                                <span class="price-add d-none d-sm-block d-md-none d-lg-block">₹{{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                                            </button>
                                        </div>
                                        <a href="#" class="tf-btn type-xl btn-primary animate-btn w-100">
                                            Buy It Now
                                        </a>
                                    </div>
                                </div>
                                <div class="tf-product-extra-link">
                                    <a href="#" class="product-extra-icon link"><i class="icon icon-ArrowsLeftRight"></i>Compare</a>
                                    <a href="#ask" data-bs-toggle="modal" class="product-extra-icon link"><i class="icon icon-Question"></i>Ask A Question</a>
                                    <a href="#share" data-bs-toggle="modal" class="product-extra-icon link"><i class="icon icon-ShareNetwork"></i>Share</a>
                                </div>
                                <div class="br-line"></div>
                                <div class="tf-product-variant">
                                    <div class="variant-picker-item">
                                        <div class="variant-picker-label text-caption-02">
                                            <i class="icon icon-Timer"></i>
                                            <p>Estimated Delivery: <span class="fw-semibold">3-7 Days</span> (All over India)</p>
                                        </div>
                                    </div>
                                    <div class="variant-picker-item">
                                        <div class="variant-picker-label text-caption-02">
                                            <i class="icon icon-ArrowClockwise"></i>
                                            <p>Return within <span class="fw-semibold">7 Days</span> of purchase.</p>
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
                                <p class="price__prd fw-semibold">₹{{ number_format($product->sale_price ?? $product->price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="tf-sticky-atc-infos">
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
                                Add To Cart - ₹{{ number_format($product->sale_price ?? $product->price, 2) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Sticky ATC -->

        <!-- Product Description -->
        <section class="section-product-description flat-spacing flat-animate-tab">
            <div class="container">
                <ul class="tab-btn-wrap-v1" role="tablist">
                    <li class="nav-tab-item" role="presentation">
                        <a href="#description" data-bs-toggle="tab" class="tf-btn-tab active" role="tab">
                            <span class="h5 fw-medium">Description</span>
                        </a>
                    </li>
                    <li class="nav-tab-item" role="presentation">
                        <a href="#shipping-returns" data-bs-toggle="tab" class="tf-btn-tab" role="tab">
                            <span class="h5 fw-medium">Shipping & Returns</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="description" role="tabpanel">
                        <div class="tab-content_desc">
                            <div class="box-desc">
                                <h5 class="desc_title">Product Description</h5>
                                <div class="desc_info">
                                    {!! $product->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="shipping-returns" role="tabpanel">
                        <div class="tab-content_desc">
                            <div class="box-desc">
                                <h5 class="desc_title">Shipping Information</h5>
                                <p class="cl-text-2">We offer nationwide shipping across India. Delivery typically takes 3-7 business days depending on your location.</p>
                                <h5 class="desc_title mt-20">Return Policy</h5>
                                <p class="cl-text-2">Returns are accepted within 7 days of purchase. Items must be in their original condition and packaging.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Product Description -->

        <!-- Related Product -->
        @if($relatedProducts->isNotEmpty())
        <div class="flat-spacing flat-animate-tab pt-0">
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

    <!-- Modal Ask -->
    <div class="modal modalCentered fade modal-log modal-ask" id="ask">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <span class="icon-close-popup" data-bs-dismiss="modal"><i class="icon icon-X2"></i></span>
                <div class="modal-heading text-center">
                    <h3 class="title-pop mb-8">Ask A Question</h3>
                    <p class="desc-pop cl-text-2">Have a question about {{ $product->name }}? Ask us today!</p>
                </div>
                <div class="modal-main">
                    <form class="form-log mb-20">
                        <div class="form-content">
                            <fieldset class="tf-field">
                                <label class="tf-lable fw-medium">Your Name<span class="text-primary">*</span></label>
                                <input type="text" placeholder="Your Name*" required>
                            </fieldset>
                            <fieldset class="tf-field">
                                <label class="tf-lable fw-medium">Your Email<span class="text-primary">*</span></label>
                                <input type="email" placeholder="Your Email*" required>
                            </fieldset>
                            <fieldset class="tf-field">
                                <label class="tf-lable fw-medium">Your Message<span class="text-primary">*</span></label>
                                <textarea placeholder="Your Message*" required></textarea>
                            </fieldset>
                        </div>
                        <div class="group-action">
                            <button type="submit" class="tf-btn animate-btn w-100">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Share -->
    <div class="modal modalCentered fade modal-share" id="share">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heading d-flex align-items-center justify-content-between">
                    <h4 class="title-pop">Share</h4>
                    <span class="cs-pointer d-flex link" data-bs-dismiss="modal"><i class="icon-X2 fs-24"></i></span>
                </div>
                <div class="modal-main">
                    <ul class="tf-social-icon-2 hv-dark mb-20">
                        <li><a href="#"><i class="icon icon-FacebookLogo"></i></a></li>
                        <li><a href="#"><i class="icon icon-XLogo"></i></a></li>
                        <li><a href="#"><i class="icon icon-InstagramLogo"></i></a></li>
                    </ul>
                    <div class="wrap-code btn-coppy-text">
                        <p class="coppyText cl-text-2" id="coppyText">{{ Request::url() }}</p>
                        <div class="btn-action-copy tf-btn" onclick="copyToClipboard()">Copy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                
                function handleAddToCart(event) {
                    const isSticky = event.target.id === 'sticky-add-to-cart-btn';
                    
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
                            alert(data.message);
                            // Update cart counters if any
                            const cartCounters = document.querySelectorAll('.toolbar-count, .cart-count');
                            cartCounters.forEach(counter => {
                                counter.textContent = data.cart_count;
                            });
                        } else {
                            alert('Something went wrong. Please try again.');
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
