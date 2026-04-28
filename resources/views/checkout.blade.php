<x-layout :seo="$seo" title="Checkout">
    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Checkout</p>
                </div>
                <h1>Checkout</h1>
                <p class="text-body-1 cl-text-2">Secure order confirmation will be enabled here.</p>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="text-center border rounded-20 p-40 bg-light">
                <h2 class="h4 mb-12">Checkout is being prepared</h2>
                <p class="text-body-2 cl-text-2 mb-0">This page is intentionally set to <code>noindex</code> until the purchase flow is complete.</p>
            </div>
        </div>
    </section>
</x-layout>
