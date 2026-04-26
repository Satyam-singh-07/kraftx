<x-layout title="Home - TheKraftX">
    <!-- Slide Show -->
    <div class="tf-slideshow tf-btn-swiper-main hover-sw-nav">
        <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" data-loop="true" data-effect="fade"
            data-delay="3000">
            <div class="swiper-wrapper">
                @if(isset($banners) && $banners->isNotEmpty())
                @php
                    $bannerWidths = [300, 400, 500, 600, 700, 800, 900, 1000, 1200, 1400, 1600, 1800, 2000, 2200, 2400, 2600, 2800, 3000, 3200];
                @endphp
                @foreach($banners as $banner)
                <div class="swiper-slide">
                    <div class="slideshow-wrap">
                        <div class="sld_image">
                            @php
                                $bannerUrl = Storage::url($banner->image);
                                $bannerSep = str_contains($bannerUrl, '?') ? '&' : '?';
                                $bannerSrcset = collect($bannerWidths)
                                    ->map(fn ($w) => $bannerUrl . $bannerSep . 'width=' . $w . ' ' . $w . 'w')
                                    ->implode(', ');
                                $bannerSrc = $bannerUrl . $bannerSep . 'width=3840';

                                $mobileUrl = $banner->mobile_image ? Storage::url($banner->mobile_image) : null;
                                $mobileSep = $mobileUrl && str_contains($mobileUrl, '?') ? '&' : '?';
                                $mobileSrcset = $mobileUrl
                                    ? collect($bannerWidths)
                                        ->map(fn ($w) => $mobileUrl . $mobileSep . 'width=' . $w . ' ' . $w . 'w')
                                        ->implode(', ')
                                    : null;
                                $mobileSrc = $mobileUrl ? $mobileUrl . $mobileSep . 'width=1200' : null;
                            @endphp
                            @if($mobileUrl)
                            <picture>
                                <source media="(max-width: 767px)" srcset="{{ $mobileSrcset }}" sizes="100vw">
                                <img loading="lazy" fetchpriority="low" width="1920" height="730" src="{{ $bannerSrc }}" srcset="{{ $bannerSrcset }}" sizes="100vw" alt="{{ $banner->title ?? 'Banner' }}">
                            </picture>
                            @else
                            <img loading="lazy" fetchpriority="low" width="1920" height="730" src="{{ $bannerSrc }}" srcset="{{ $bannerSrcset }}" sizes="100vw" alt="{{ $banner->title ?? 'Banner' }}">
                            @endif
                        </div>
                        <div class="sld_content pst-5">
                            <div class="container">
                                <div class="content-sld_wrap text-center">
                                    <div class="heading">
                                        @if(!empty($banner->subtitle))
                                        <p class="sub-text_sld text-body-1 text-white fade-item fade-item-1 mb-15">
                                            {{ $banner->subtitle }}
                                        </p>
                                        @endif
                                        @if(!empty($banner->title))
                                        <p class="title_sld text-display fw-medium text-white fade-item fade-item-2">
                                            {{ $banner->title }}
                                        </p>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                @foreach([
                    ['img' => 'slider-1.jpg', 'title' => 'Elevate Your Style with Timeless Elegance'],
                    ['img' => 'slider-2.jpg', 'title' => 'Discover a New Level of Timeless Style'],
                    ['img' => 'slider-3.jpg', 'title' => 'Define Your Style through Elegant Simplicity']
                ] as $slide)
                <div class="swiper-slide">
                    <div class="slideshow-wrap">
                        <div class="sld_image">
                            <img loading="lazy" width="1920" height="730" src="{{ asset('assets/images/slider/'.$slide['img']) }}" alt="Image">
                        </div>
                        <div class="sld_content pst-5">
                            <div class="container">
                                <div class="content-sld_wrap text-center">
                                    <div class="heading">
                                        <p class="sub-text_sld text-body-1 text-white fade-item fade-item-1 mb-15">
                                            DISCOVER THE ART OF MODERN DRESSING
                                        </p>
                                        <p class="title_sld text-display fw-medium text-white fade-item fade-item-2">
                                            {!! $slide['title'] !!}
                                        </p>
                                    </div>
                                    <div class="fade-item fade-item-3">
                                        <a href="#" class="tf-btn btn-white">Shop Styles</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <div class="sw-line-default tf-sw-pagination"></div>
        </div>
        <div class="group-nav-action">
            <div class="container-full">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="tf-sw-nav text-white link nav-prev-swiper">
                        <i class="icon icon-ArrowLongLeft"></i>
                    </div>
                    <div class="tf-sw-nav text-white link nav-next-swiper">
                        <i class="icon icon-ArrowLongRight"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Slide Show -->

    <!-- Category -->
    <section class="flat-spacing">
        <div class="container">
            <div class="sect-heading type-2 text-center wow fadeInUp">
                <h3 class="s-title">Shop By Categories</h3>
                <p class="s-desc text-body-1 cl-text-2">Top styles everyone’s talking about.</p>
            </div>
            <div dir="ltr" class="swiper tf-swiper" data-preview="6" data-tablet="4" data-mobile-sm="3"
                data-mobile="2" data-space-lg="30" data-space-md="15" data-space="10" data-pagination="2"
                data-pagination-sm="3" data-pagination-md="4" data-pagination-lg="6">
                <div class="swiper-wrapper">
                    @if(isset($collections) && $collections->isNotEmpty())
                    @php
                        $fallbackCategoryImages = ['cate-1.jpg', 'cate-2.jpg', 'cate-3.jpg', 'cate-4.jpg', 'cate-5.jpg', 'cate-6.jpg'];
                    @endphp
                    @foreach($collections as $collection)
                    <div class="swiper-slide wow fadeInUp">
                        <a href="{{ route('collection.show', $collection->slug) }}" class="category-v01 hover-img">
                            <div class="cate-image img-style rounded-circle overflow-hidden" style="aspect-ratio: 1/1;">
                                <img loading="lazy" width="210" height="210"
                                    src="{{ $collection->image ? Storage::url($collection->image) : asset('assets/images/category/'.$fallbackCategoryImages[$loop->index % count($fallbackCategoryImages)]) }}"
                                    alt="{{ $collection->name }}" class="rounded-circle object-fit-cover w-100 h-100">
                            </div>
                            <p class="cate-name h5 text-center link link-underline">{{ $collection->name }}</p>
                        </a>
                    </div>
                    @endforeach
                   

                    @endif
                </div>
                <div class="sw-line-default style-2 tf-sw-pagination"></div>
            </div>
        </div>
    </section>
    <!-- /Category -->

    <!-- Top Pick -->
    <section class="flat-spacing pt-0">
        <div class="container">
            <div class="sect-heading type-2 text-center wow fadeInUp">
                <h3 class="s-title">Today's Top Picks</h3>
                <p class="s-desc text-body-1 cl-text-2">Fresh styles just in! Elevate your look.</p>
            </div>
            <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3" data-mobile-sm="2"
                data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="2"
                data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                <div class="swiper-wrapper">
                    @php
                        $topPickProducts = $topPicks ?? collect();
                    @endphp
                    @if($topPickProducts->isNotEmpty())
                        @foreach($topPickProducts as $product)
                        <div class="swiper-slide wow fadeInUp">
                            <x-product-card :product="$product" />
                        </div>
                        @endforeach
                  
                    @endif
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </div>
    </section>
    <!-- /Top Pick -->



     <!-- Collection -->
        <div class="section-banner-collection">
            <div class="container">
                
                <div class="tf-grid-layout sm-col-2 gap-10">
                    <div class="box-image_v01">
                        <a href="{{ isset($collections) && $collections->where('name', 'Banners')->first() ? route('collection.show', $collections->where('name', 'Banners')->first()->slug) : '#' }}" class="box-image_img img-style">
                            <img loading="lazy" width="700" height="933" src="assets/images/bahubali-hanuman.png"
                                alt="Image">
                        </a>
                        <div class="box-image_content">
                            <a href="{{ isset($collections) && $collections->where('name', 'Banners')->first() ? route('collection.show', $collections->where('name', 'Banners')->first()->slug) : '#' }}"
                                class="title h3 fw-medium text-white link-underline-white text-decoration-thickness">
                                 <!-- Meditating Lord Hanuman -->
                            </a>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-10">
                        <div class="box-image_v01 h-100">
                            <a href="{{ isset($collections) && $collections->where('name', 'New Arrival')->first() ? route('collection.show', $collections->where('name', 'New Arrival')->first()->slug) : '#' }}" class="box-image_img img-style">
                                <img loading="lazy" width="700" height="461" src="assets/images/hanuman.png"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="{{ isset($collections) && $collections->where('name', 'New Arrival')->first() ? route('collection.show', $collections->where('name', 'New Arrival')->first()->slug) : '#' }}"
                                    class="title h3 fw-medium text-white link-underline-white text-decoration-thickness">
                                    <!-- Mahabali Lord Hanuman -->
                                </a>
                            </div>
                        </div>
                        <div class="box-image_v01 h-100">
                            <a href="{{ isset($collections) && $collections->where('name', 'Best Seller')->first() ? route('collection.show', $collections->where('name', 'Best Seller')->first()->slug) : '#' }}" class="box-image_img img-style">
                                <img loading="lazy" width="700" height="461" src="assets/images/ganesh-ji.png"
                                    alt="Image">
                            </a>
                            <div class="box-image_content">
                                <a href="{{ isset($collections) && $collections->where('name', 'Best Seller')->first() ? route('collection.show', $collections->where('name', 'Best Seller')->first()->slug) : '#' }}"
                                    class="title h3 fw-medium text-white link-underline-white text-decoration-thickness">
                                    <!-- Shop Essentials -->
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Collection -->
        <!-- Top Pick -->
        <section class="flat-spacing">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Top Trending
                    </h3>
                    <p class="s-desc text-body-1 cl-text-2">
                        Browse our Top Trending picks loved by all.
                    </p>
                </div>
                <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3" data-mobile-sm="2"
                    data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="2"
                    data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                    <div class="swiper-wrapper">
                        @foreach ($trendingProducts as $product)
                            <div class="swiper-slide">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Top Pick -->
        <!-- Lookbook -->
        <div class="themesFlat">
            <div class="tf-grid-layout xl-col-2 gap-10 mb-10">
                <div class="banner-lookbook wrap-lookbook_hover">
                    <img class="img-banner" loading="lazy" width="955" height="640"
                        src="assets/images/section/banner-lookbook-1.jpg" alt="Image">
                    <div class="lookbook-item position1">
                        <div class="dropdown dropup-center dropdown-custom dropend">
                            <div role="dialog" class="tf-pin-btn bundle-pin-item swiper-button" data-slide="0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span></span>
                            </div>
                            <div class="dropdown-menu">
                                <div class="lookbook-product">
                                    <a href="product-detail.html" class="image">
                                        <img width="88" height="88" src="assets/images/product/square/product-10.jpg"
                                            alt="Product">
                                    </a>
                                    <div class="content">
                                        <a href="product-detail.html"
                                            class="name-prd link fw-medium lh-24 text-line-clamp-2">
                                            High neck midi wool coat
                                        </a>
                                        <div class="price-wrap">
                                            <span class="price-new text-primary fw-semibold">₹29,99</span>
                                            <span class="price-old text-caption-01 cl-text-3">₹39.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lookbook-item position2">
                        <div class="dropdown dropup-center dropdown-custom dropstart">
                            <div role="dialog" class="tf-pin-btn bundle-pin-item swiper-button" data-slide="0" id="pin2"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span></span>
                            </div>
                            <div class="dropdown-menu">
                                <div class="lookbook-product">
                                    <a href="product-detail.html" class="image">
                                        <img width="88" height="88" src="assets/images/product/square/product-3_3.jpg"
                                            alt="Product">
                                    </a>
                                    <div class="content">
                                        <a href="product-detail.html"
                                            class="name-prd link fw-medium lh-24 text-line-clamp-2">
                                            High neck midi wool coat
                                        </a>
                                        <div class="price-wrap">
                                            <span class="price-new text-primary fw-semibold">₹49,99</span>
                                            <span class="price-old text-caption-01 cl-text-3">₹59.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="banner-lookbook wrap-lookbook_hover">
                    <img class="img-banner" loading="lazy" width="955" height="640"
                        src="assets/images/section/banner-lookbook-2.jpg" alt="Image">
                    <div class="lookbook-item position3">
                        <div class="dropdown dropup-center dropdown-custom dropstart">
                            <div role="dialog" class="tf-pin-btn bundle-pin-item swiper-button" data-slide="0"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span></span>
                            </div>
                            <div class="dropdown-menu">
                                <div class="lookbook-product">
                                    <a href="product-detail.html" class="image">
                                        <img width="88" height="88" src="assets/images/product/square/product-11.jpg"
                                            alt="Product">
                                    </a>
                                    <div class="content">
                                        <a href="product-detail.html"
                                            class="name-prd link fw-medium lh-24 text-line-clamp-2">
                                            High neck midi wool coat
                                        </a>
                                        <div class="price-wrap">
                                            <span class="price-new text-primary fw-semibold">₹69,99</span>
                                            <span class="price-old text-caption-01 cl-text-3">₹79.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-main-2">
                <div class="infiniteSlide-cls wow fadeInUp">
                    <div class="infiniteSlide infiniteSlide-wrapper" data-clone="3">
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Style in Motion</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-1.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Own Your Look</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-2.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Chic by Nature</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-3.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Effortless Elegance</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-4.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Made to Stand Out</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-5.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                        <div class="infiniteSlide-item">
                            <a href="shop-default.html" class="cls-wrap">
                                <h4>Confidence in Every Step</h4>
                                <div class="img-cls">
                                    <img loading="lazy" width="80" height="80" src="assets/images/collection/cls-3.jpg"
                                        alt="Image">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Lookbook -->
        <!-- Testimonial -->
        <section class="flat-spacing">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Customer Say!
                    </h3>
                    <p class="s-desc text-body-1 cl-text-2">
                        Our customers adore our products, and we constantly aim to delight them.
                    </p>
                </div>
                <div dir="ltr" class="swiper tf-swiper" data-preview="2" data-tablet="2" data-mobile-sm="1"
                    data-mobile="1" data-space-lg="60" data-space-md="30" data-space="15" data-pagination="1"
                    data-pagination-sm="2" data-pagination-md="2" data-pagination-lg="2">
                    <div class="swiper-wrapper">
                        <!-- slide 1 -->
                        <div class="swiper-slide">
                            <div class="testimonial-v01 style-1 style-def wow fadeInLeft">
                                <div class="tes-image">
                                    <img loading="lazy" width="285" height="380"
                                        src="assets/images/testimonial/tes-1.jpg" alt="Image">
                                </div>
                                <div class="tes-content">
                                    <div class="star-wrap d-flex align-items-center">
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                    </div>
                                    <div class="tes_author">
                                        <p class="author-name h5">Emma Collins</p>
                                        <div class="br-line"></div>
                                        <div class="author-verified">
                                            <i class="icon icon-CheckCircle1"></i>
                                            <span class="cl-text-2">
                                                Verified Buyer
                                            </span>
                                        </div>
                                    </div>
                                    <p class="tes_text h6">
                                        “Totally obsessed with this outfit! The fit is perfect, the fabric feels
                                        premium,
                                        and I’ve been getting compliments non-stop. It instantly lifts my confidence —
                                        such
                                        a great find!”
                                    </p>
                                    <div class="tes_product">
                                        <div class="product-image">
                                            <img class="aspect-ratio-1 object-fit-cover" loading="lazy" width="60"
                                                height="60" src="assets/images/product/product-4.jpg" alt="Image">
                                        </div>
                                        <div class="product-infor">
                                            <a href="product-detail.html" class="link fw-medium lh-24">
                                                High neck midi wool coat
                                            </a>
                                            <p class="prd_price fw-semibold text-primary">₹29.99</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- slide 2 -->
                        <div class="swiper-slide">
                            <div class="testimonial-v01 style-1 style-def wow fadeInLeft" data-wow-delay="0.1s">
                                <div class="tes-image">
                                    <img loading="lazy" width="285" height="380"
                                        src="assets/images/testimonial/tes-2.jpg" alt="Image">
                                </div>
                                <div class="tes-content">
                                    <div class="star-wrap d-flex align-items-center">
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                        <i class="icon icon-Star-thin fs-24"></i>
                                    </div>
                                    <div class="tes_author">
                                        <p class="author-name h5">Sophia Ramirez</p>
                                        <div class="br-line"></div>
                                        <div class="author-verified">
                                            <i class="icon icon-CheckCircle1"></i>
                                            <span class="cl-text-2">
                                                Verified Buyer
                                            </span>
                                        </div>
                                    </div>
                                    <p class="tes_text h6">
                                        “I’m amazed by how comfortable yet stylish this piece is. It pairs effortlessly
                                        with everything, and the quality really stands out. Definitely becoming my go-to
                                        for everyday looks!”
                                    </p>
                                    <div class="tes_product">
                                        <div class="product-image">
                                            <img class="aspect-ratio-1 object-fit-cover" loading="lazy" width="60"
                                                height="60" src="assets/images/product/product-6.jpg" alt="Image">
                                        </div>
                                        <div class="product-infor">
                                            <a href="product-detail.html" class="link fw-medium lh-24">
                                                Square metallic frame sunglasses
                                            </a>
                                            <p class="prd_price fw-semibold text-primary">₹29.99</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-line-default style-2 tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Testimonial -->


         <!-- Blog -->
        <section class="flat-spacing pb-3">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Handicraft Guides, Idol Meaning & Decor Tips
                    </h3>
                    <p class="s-desc text-body-1 cl-text-2">
                        Find expert tips on choosing idols, understanding symbolism, and decorating your space beautifully.
                    </p>
                </div>
                <div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="2" data-mobile-sm="2"
                    data-mobile="1" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="1"
                    data-pagination-sm="2" data-pagination-md="2" data-pagination-lg="3">
                    <div class="swiper-wrapper">
                        @foreach($posts as $post)
                            <div class="swiper-slide">
                                <article class="article-blog hover-img wow fadeInUp">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="blog-image img-style">
                                        @if($post->featured_image)
                                            <img loading="lazy" width="450" height="337" src="{{ Storage::url($post->featured_image) }}"
                                                alt="{{ $post->title }}">
                                        @else
                                            <img loading="lazy" width="450" height="337" src="{{ asset('assets/images/blog/blog-placeholder.jpg') }}"
                                                alt="{{ $post->title }}">
                                        @endif
                                    </a>
                                    <div class="blog-content">
                                        <p class="entry-date text-caption-01 fw-semibold cl-text-3">{{ $post->published_at->format('d F') }}</p>
                                        <h5 class="entry-title">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="link-underline link text-capitalize">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                        <p class="entry-desc cl-text-2">
                                            {{ Str::limit($post->excerpt, 120) }}
                                        </p>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-line-default style-2 tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Blog -->





        <!-- Gallery -->
        <section class="themesFlat">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Shop Instagram
                    </h3>
                    <p class="s-desc text-body-1 cl-text-2">
                        Elevate your wardrobe with fresh finds today!
                    </p>
                </div>
                <div dir="ltr" class="swiper tf-swiper" data-preview="5" data-tablet="3" data-mobile-sm="3"
                    data-mobile="2" data-space="10" data-pagination="2" data-pagination-sm="3" data-pagination-md="4"
                    data-pagination-lg="5">
                    <div class="swiper-wrapper">
                        <!-- slide 1 -->
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                                <div class="image img-style">
                                    <img loading="lazy" width="274" height="274"
                                        src="assets/images/gallery/gallery-1.jpg" alt="Image">
                                </div>
                                <a href="product-detail.html" class="box-icon hover-tooltip">
                                    <span class="icon icon-Eye"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                        <!-- slide 2 -->
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                                <div class="image img-style">
                                    <img loading="lazy" width="274" height="274"
                                        src="assets/images/gallery/gallery-2.jpg" alt="Image">
                                </div>
                                <a href="product-detail.html" class="box-icon hover-tooltip">
                                    <span class="icon icon-Eye"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                        <!-- slide 3 -->
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                                <div class="image img-style">
                                    <img loading="lazy" width="274" height="274"
                                        src="assets/images/gallery/gallery-3.jpg" alt="Image">
                                </div>
                                <a href="product-detail.html" class="box-icon hover-tooltip">
                                    <span class="icon icon-Eye"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                        <!-- slide 4 -->
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                                <div class="image img-style">
                                    <img loading="lazy" width="274" height="274"
                                        src="assets/images/gallery/gallery-4.jpg" alt="Image">
                                </div>
                                <a href="product-detail.html" class="box-icon hover-tooltip">
                                    <span class="icon icon-Eye"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                        <!-- slide 5 -->
                        <div class="swiper-slide">
                            <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                                <div class="image img-style">
                                    <img loading="lazy" width="274" height="274"
                                        src="assets/images/gallery/gallery-5.jpg" alt="Image">
                                </div>
                                <a href="product-detail.html" class="box-icon hover-tooltip">
                                    <span class="icon icon-Eye"></span>
                                    <span class="tooltip">View product</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Gallery -->
        <!-- Box Icon -->
        <div class="flat-spacing">
            <div class="container">
                <div dir="ltr" class="swiper tf-swiper" data-preview="4" data-tablet="3" data-mobile-sm="2"
                    data-mobile="1" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="1"
                    data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                    <div class="swiper-wrapper">
                        <!-- slide 1 -->
                        <div class="swiper-slide">
                            <div class="box-icon_V01 wow fadeInLeft">
                                <span class="icon">
                                    <i class="icon-ArrowUDownLeft"></i>
                                </span>
                                <div class="content">
                                    <p class="title h6">14-Day Returns</p>
                                    <p class="text cl-text-2">Risk-free shopping with easy returns.</p>
                                </div>
                            </div>
                        </div>
                        <!-- slide 2 -->
                        <div class="swiper-slide">
                            <div class="box-icon_V01 wow fadeInLeft">
                                <span class="icon">
                                    <i class="icon-Package"></i>
                                </span>
                                <div class="content">
                                    <p class="title h6">Free Shipping</p>
                                    <p class="text cl-text-2">No extra costs, just the price you see.</p>
                                </div>
                            </div>
                        </div>
                        <!-- slide 3 -->
                        <div class="swiper-slide">
                            <div class="box-icon_V01 wow fadeInLeft">
                                <span class="icon">
                                    <i class="icon-Headset"></i>
                                </span>
                                <div class="content">
                                    <p class="title h6">24/7 Support</p>
                                    <p class="text cl-text-2">24/7 support, always here just for you.</p>
                                </div>
                            </div>
                        </div>
                        <!-- slide 4 -->
                        <div class="swiper-slide">
                            <div class="box-icon_V01 wow fadeInLeft">
                                <span class="icon">
                                    <i class="icon-SealPercent"></i>
                                </span>
                                <div class="content">
                                    <p class="title h6">Member Discounts</p>
                                    <p class="text cl-text-2">Special prices for our loyal customers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sw-line-default style-2 tf-sw-pagination"></div>
                </div>
            </div>
        </div>
        <!-- /Box Icon -->

</x-layout>
