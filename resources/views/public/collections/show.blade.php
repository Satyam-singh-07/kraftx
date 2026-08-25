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

        .collection-products-grid > .collection-product-item {
            display: flex;
        }

        .collection-products-grid .card-product {
            width: 100%;
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
                    <div id="collection-products-grid" class="collection-products-grid row g-3 g-md-4">
                        @include('public.collections._products', ['products' => $products->getCollection()])
                    </div>
                    <div id="collection-load-status" class="mt-30 text-center text-body-2 cl-text-2" aria-live="polite"></div>
                @else
                    <div class="collection-empty text-center">
                        <p class="text-body-1 cl-text-2 mb-0">No products found in this collection.</p>
                        <a href="{{ route('home') }}" class="tf-btn animate-btn mt-20">Back To Home</a>
                    </div>
                @endif
            </div>

        </div>
    </section>
    <!-- /Shop -->
    <x-slot name="scripts">
        @if($products->hasMorePages())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const grid = document.getElementById('collection-products-grid');
                    const status = document.getElementById('collection-load-status');
                    if (!grid || !status) return;

                    let nextPage = {{ $products->currentPage() + 1 }};
                    let hasMore = true;
                    let loading = false;

                    const loadMoreProducts = async function () {
                        if (loading || !hasMore) return;
                        loading = true;
                        status.textContent = 'Loading more products...';

                        try {
                            const url = new URL(@json(route('collection.show', $collection->slug)), window.location.origin);
                            url.searchParams.set('page', nextPage);
                            url.searchParams.set('load_more', '1');

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) throw new Error('Unable to load products');
                            const data = await response.json();
                            grid.insertAdjacentHTML('beforeend', data.html || '');
                            hasMore = Boolean(data.has_more);
                            nextPage = Number(data.next_page || nextPage + 1);
                            status.textContent = hasMore ? '' : 'You have reached the end of this collection.';
                        } catch (error) {
                            status.textContent = 'Unable to load more products. Please try again.';
                        } finally {
                            loading = false;
                        }
                    };

                    const sentinel = document.createElement('div');
                    sentinel.setAttribute('aria-hidden', 'true');
                    status.before(sentinel);
                    new IntersectionObserver(function (entries) {
                        if (entries.some(entry => entry.isIntersecting)) loadMoreProducts();
                    }, { rootMargin: '500px 0px' }).observe(sentinel);
                });
            </script>
        @endif
    </x-slot>
</x-layout>
