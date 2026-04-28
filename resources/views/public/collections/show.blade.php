<x-layout :seo="$seo" title="{{ $collection->name }} - Collection">
    <style>
        .collection-hero {
            padding-top: 36px;
        }

        .collection-hero .main-page-title {
            max-width: 760px;
            margin: 0 auto;
        }

        .collection-showcase-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .collection-showcase-copy {
            max-width: 620px;
        }

        .collection-showcase-copy h4 {
            margin-bottom: 8px;
        }

        .collection-showcase-copy p {
            margin: 0;
        }

        .collection-showcase-meta {
            flex-shrink: 0;
            color: var(--text-3);
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .collection-products-slider .swiper-slide {
            height: auto;
        }

        .collection-empty {
            padding: 64px 24px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #faf8f4;
        }

        @media (max-width: 767px) {
            .collection-showcase-head {
                flex-direction: column;
                align-items: start;
                margin-bottom: 20px;
            }
        }
    </style>

    <!-- Page Title -->
    <section class="section-page-title text-center flat-spacing-2 pb-0 collection-hero">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">{{ $collection->name }}</p>
                </div>
                <h3>{{ $collection->name }}</h3>
                @if($collection->description)
                    <p class="text-body-1 cl-text-2">
                        {!! nl2br(e($collection->description)) !!}
                    </p>
                @endif
            </div>
        </div>
    </section>
    <!-- /Page Title -->

    <!-- Shop -->
    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="collection-showcase">
                <div class="collection-showcase-head">
                    <div class="collection-showcase-copy">
                        <h4>Explore {{ $collection->name }}</h4>
                        <p class="text-body-1 cl-text-2">
                            Browse this collection in the same horizontal product style used on the home page.
                        </p>
                    </div>
                    <div class="collection-showcase-meta">{{ $products->total() }} products</div>
                </div>

                @if($products->count())
                    <div dir="ltr" class="swiper tf-swiper wrap-sw-over collection-products-slider"
                        data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="1.2"
                        data-space-lg="30" data-space-md="20" data-space="12"
                        data-pagination="1" data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                        <div class="swiper-wrapper">
                            @foreach($products as $product)
                                <div class="swiper-slide wow fadeInUp">
                                    <x-product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>
                        <div class="sw-dot-default tf-sw-pagination"></div>
                    </div>
                @else
                    <div class="collection-empty text-center">
                        <p class="text-body-1 cl-text-2 mb-0">No products found in this collection.</p>
                        <a href="{{ route('home') }}" class="tf-btn animate-btn mt-20">Back To Home</a>
                    </div>
                @endif
            </div>

            @if($products->hasPages())
                <div class="tf-pagination mt-40">
                    {{ $products->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
    <!-- /Shop -->
</x-layout>
