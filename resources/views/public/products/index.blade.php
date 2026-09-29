<x-layout :seo="$seo" title="All Products">
    <style>
        .product-hero {
            padding-top: 20px !important;
            padding-bottom: 20px !important;
        }
        .product-hero h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>

    <!-- Page Title -->
    <section class="section-page-title text-center flat-spacing-2 pb-0 product-hero">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">All Products</p>
                </div>
                <h3>All Products</h3>
                <p class="text-body-1 cl-text-2">
                    Discover our complete range of unique, handcrafted items.
                </p>
            </div>
        </div>
    </section>
    <!-- /Page Title -->

    <section class="flat-spacing pt-0">
        <div class="container">
            <div class="row g-3 g-md-4" id="products-container">
                @include('public.products._list', ['products' => $products])
            </div>
            
            <div id="loading" class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </section>

    <x-slot name="scripts">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let page = 1;
            let loading = false;
            let hasMore = {{ $products->hasMorePages() ? 'true' : 'false' }};

            const container = document.getElementById('products-container');
            const loadingIndicator = document.getElementById('loading');

            function isNearPageBottom() {
                return window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 500;
            }

            function maybeLoadMore() {
                if (!loading && hasMore && isNearPageBottom()) {
                    loadMoreProducts();
                }
            }

            window.addEventListener('scroll', maybeLoadMore);

            function loadMoreProducts() {
                if (loading || !hasMore) return;

                loading = true;
                const requestedPage = page + 1;
                loadingIndicator.style.display = 'block';

                $.ajax({
                    url: "{{ route('products.index') }}",
                    data: { page: requestedPage },
                    type: "get",
                    dataType: "json"
                })
                .done(function(response) {
                    if (response.html) {
                        container.insertAdjacentHTML('beforeend', response.html);
                    }

                    page = requestedPage;
                    hasMore = response.has_more === true;
                    loadingIndicator.style.display = 'none';
                    loading = false;

                    // Load another page only when the viewport still has no scroll range.
                    maybeLoadMore();
                })
                .fail(function() {
                    loadingIndicator.style.display = 'none';
                    loading = false;
                });
            }
        });
    </script>
    </x-slot>
</x-layout>
