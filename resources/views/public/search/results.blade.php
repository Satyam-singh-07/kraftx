<x-layout :seo="$seo" title="Search results for '{{ $query }}'">
    <style>
        .search-hero {
            padding-top: 36px;
        }

        .search-hero .main-page-title {
            max-width: 760px;
            margin: 0 auto;
        }

        .search-showcase-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .search-showcase-copy {
            max-width: 620px;
        }

        .search-showcase-copy h4 {
            margin-bottom: 8px;
        }

        .search-showcase-copy p {
            margin: 0;
        }

        .search-showcase-meta {
            flex-shrink: 0;
            color: var(--text-3);
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .search-empty {
            padding: 64px 24px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #faf8f4;
        }

        @media (max-width: 767px) {
            .search-showcase-head {
                flex-direction: column;
                align-items: start;
                margin-bottom: 20px;
            }
        }
    </style>

    <!-- Page Title -->
    <section class="section-page-title text-center flat-spacing-2 pb-0 search-hero">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Search results</p>
                </div>
                <h3>Search results for "{{ $query }}"</h3>
                <p class="text-body-1 cl-text-2">
                    Found {{ $products->total() }} results for your search.
                </p>
            </div>
        </div>
    </section>
    <!-- /Page Title -->

    <!-- results -->
    <section class="flat-spacing pt-40">
        <div class="container">
            @if($products->count())
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4 col-lg-3 wow fadeInUp">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="search-empty text-center">
                    <p class="text-body-1 cl-text-2 mb-0">No products found matching your search "{{ $query }}".</p>
                    <div class="mt-24 max-w-400 mx-auto">
                        <form action="{{ route('search.results') }}" method="GET" class="form-search-nav style-2">
                            <fieldset>
                                <input type="text" name="q" value="{{ $query }}" placeholder="Search again..." required>
                            </fieldset>
                            <button type="submit" class="btn-action">
                                <i class="icon icon-MagnifyingGlass"></i>
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('home') }}" class="tf-btn animate-btn mt-30">Back To Home</a>
                </div>
            @endif

            @if($products->hasPages())
                <div class="tf-pagination mt-40">
                    {{ $products->appends(['q' => $query])->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
    <!-- /results -->
</x-layout>
