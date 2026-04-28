<x-layout :seo="$seo" title="Deals & Offers">
    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Deals</p>
                </div>
                <h1>Active Deals & Offers</h1>
                <p class="text-body-1 cl-text-2">Limited-time promotions and seasonal picks from KraftX.</p>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-40">
        <div class="container">
            @if($deals->isNotEmpty())
                <div class="row g-4">
                    @foreach($deals as $deal)
                        <div class="col-md-6 col-lg-4">
                            <article class="border rounded-20 overflow-hidden h-100 bg-white">
                                @if($deal->banner_image)
                                    <a href="{{ route('deals.show', $deal->slug) }}">
                                        <img
                                            src="{{ asset('storage/' . $deal->banner_image) }}"
                                            alt="{{ $deal->title }}"
                                            loading="lazy"
                                            class="w-100"
                                            style="aspect-ratio: 16/10; object-fit: cover;"
                                        >
                                    </a>
                                @endif
                                <div class="p-24">
                                    <p class="text-caption-02 text-uppercase cl-text-3 mb-8">Offer</p>
                                    <h3 class="h5 mb-12">
                                        <a href="{{ route('deals.show', $deal->slug) }}" class="text-dark text-decoration-none">
                                            {{ $deal->title }}
                                        </a>
                                    </h3>
                                    <p class="text-body-2 cl-text-2 mb-16">{{ \Illuminate\Support\Str::limit(strip_tags($deal->description), 110) }}</p>
                                    <a href="{{ route('deals.show', $deal->slug) }}" class="tf-btn animate-btn btn-sm">View Deal</a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-body-1 cl-text-2 mb-0">There are no active deals right now.</p>
                </div>
            @endif
        </div>
    </section>
</x-layout>
