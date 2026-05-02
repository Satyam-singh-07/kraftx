<x-layout :seo="$seo" title="All Collections">
    <style>
        .collection-hero {
            padding-top: 36px;
        }
        .collection-item-v2 {
            display: block;
            margin-bottom: 30px;
        }
        .collection-image {
            transition: transform 0.3s ease;
        }
        .collection-item-v2:hover .collection-image {
            transform: scale(1.05);
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

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="row" id="collections-container">
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
        $(document).ready(function() {
            let page = 1;
            let loading = false;
            let hasMore = {{ $collections->hasMorePages() ? 'true' : 'false' }};

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 500) {
                    if (!loading && hasMore) {
                        loadMoreCollections();
                    }
                }
            });

            function loadMoreCollections() {
                loading = true;
                page++;
                $('#loading').show();

                $.ajax({
                    url: "{{ route('collections.index') }}?page=" + page,
                    type: "get"
                })
                .done(function(data) {
                    if (data.trim() == "") {
                        hasMore = false;
                        $('#loading').hide();
                        return;
                    }
                    $('#loading').hide();
                    $("#collections-container").append(data);
                    loading = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                    $('#loading').hide();
                    loading = false;
                });
            }
        });
    </script>
    </x-slot>
</x-layout>
