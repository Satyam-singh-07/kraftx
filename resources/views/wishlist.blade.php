<x-layout :seo="$seo" title="Wishlist">
    <section class="section-page-title text-center">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Wishlist</p>
                </div>
                <h3>Wishlist</h3>
                <p class="text-body-1 cl-text-2">Save products you love and remove them anytime.</p>
            </div>
        </div>
    </section>

    <section class="flat-spacing">
        <div class="container">
            <div class="wishlist-empty text-center border rounded-20 p-40 {{ $products->isNotEmpty() ? 'd-none' : '' }}">
                <h5 class="mb-8">Your wishlist is empty</h5>
                <p class="cl-text-2 mb-20">Products you save will appear here.</p>
                <a href="{{ route('home') }}" class="tf-btn animate-btn">Continue Shopping</a>
            </div>

            <div class="wishlist-content {{ $products->isEmpty() ? 'd-none' : '' }}">
                <div class="tf-grid-layout tf-col-2 md-col-3 xl-col-4 wrapper-wishlist">
                    @foreach ($products as $product)
                        <div class="wishlist-card" data-product-id="{{ $product['id'] }}">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layout>
