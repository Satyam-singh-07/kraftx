<x-layout :seo="$seo" title="{{ $deal->title }}">
    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <a href="{{ route('deals.index') }}" class="text-caption-01 cl-text-3 link">Deals</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">{{ $deal->title }}</p>
                </div>
                <h1>{{ $deal->title }}</h1>
                @if($deal->description)
                    <p class="text-body-1 cl-text-2">{{ $deal->description }}</p>
                @endif
            </div>
        </div>
    </section>

    @if($deal->banner_image)
        <section class="pt-40">
            <div class="container">
                <img
                    src="{{ asset('storage/' . $deal->banner_image) }}"
                    alt="{{ $deal->title }}"
                    class="w-100 rounded-20"
                    style="max-height: 500px; object-fit: cover;"
                >
            </div>
        </section>
    @endif

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-12 mb-24">
                <div>
                    <h2 class="h4 mb-6">Included Products</h2>
                    <p class="text-body-2 cl-text-2 mb-0">Explore products available in this offer.</p>
                </div>
                <div class="text-caption-01 cl-text-3">{{ $deal->products->count() }} products</div>
            </div>

            @if($deal->products->isNotEmpty())
                <div class="row g-4">
                    @foreach($deal->products as $product)
                        @php
                            $image = $product->images->first() ? 'storage/' . $product->images->first()->image_path : 'assets/images/product/product-placeholder.jpg';
                            $hoverImage = $product->images->get(1) ? 'storage/' . $product->images->get(1)->image_path : $image;
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="[
                                'id' => $product->id,
                                'name' => $product->name,
                                'url' => route('product.show', $product->slug),
                                'image' => $image,
                                'hoverImage' => $hoverImage,
                                'price' => '₹' . number_format($product->sale_price ?? $product->price, 0),
                                'oldPrice' => $product->sale_price ? '₹' . number_format($product->price, 0) : null,
                                'hasSize' => $product->variants->whereNotNull('size')->isNotEmpty(),
                                'sizes' => $product->variants->whereNotNull('size')->unique('size')->pluck('size')->toArray(),
                                'badges' => [],
                            ]" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-body-1 cl-text-2 mb-0">No products are attached to this deal yet.</p>
                </div>
            @endif
        </div>
    </section>
</x-layout>
