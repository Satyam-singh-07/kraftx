<x-layout :seo="$seo" title="All Collections">
    <style>
        .collection-hero {
            padding-top: 20px !important;
            padding-bottom: 20px !important;
        }
        .collection-hero h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .collection-item-v2 {
            display: block;
            margin-bottom: 20px;
        }
        .collection-image {
            transition: transform 0.3s ease;
        }
        .collection-item-v2:hover .collection-image {
            transform: scale(1.05);
        }
        .collection-title {
            font-size: 14px;
            line-height: 1.4;
            margin-top: 8px;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>

    <!-- Page Title -->
    <section class="section-page-title text-center flat-spacing-2 pb-0 collection-hero">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">All Collections</p>
                </div>
                <h3>All Collections</h3>
                <p class="text-body-1 cl-text-2">
                    Explore our diverse range of handcrafted collections.
                </p>
            </div>
        </div>
    </section>
    <!-- /Page Title -->

    <section class="flat-spacing pt-0">
        <div class="container">
            <div class="row g-3 g-md-4" id="collections-container">
                @include('public.collections._list', ['collections' => $collections])
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
            let hasMore = {{ $collections->hasMorePages() ? 'true' : 'false' }};

            const container = document.getElementById('collections-container');
            const loadingIndicator = document.getElementById('loading');

            function isNearPageBottom() {
                return window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 500;
            }

            function maybeLoadMore() {
                if (!loading && hasMore && isNearPageBottom()) {
                    loadMoreCollections();
                }
            }

            window.addEventListener('scroll', maybeLoadMore);

            function loadMoreCollections() {
                if (loading || !hasMore) return;

                loading = true;
                const requestedPage = page + 1;
                loadingIndicator.style.display = 'block';

                $.ajax({
                    url: "{{ route('collections.index') }}",
                    data: { page: requestedPage },
                    type: "get",
                    dataType: "json"
                })
                .done(function(data) {
                    if (data.html) {
                        container.insertAdjacentHTML('beforeend', data.html);
                    }

                    page = requestedPage;
                    hasMore = data.has_more === true;
                    loadingIndicator.style.display = 'none';
                    loading = false;

                    // Fill a short viewport sequentially, stopping at the last page.
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
