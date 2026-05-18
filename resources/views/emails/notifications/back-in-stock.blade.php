@component('mail::message')
@include('emails.notifications.components.brand-header', ['logoUrl' => $logoUrl])

<div class="kx-hero">
    <p class="kx-eyebrow">Back in stock</p>
    <h1>{{ $product->name }}</h1>
    <p class="kx-copy">
        The product you asked about is available again. Stock may move quickly, so complete your purchase while it is still available.
    </p>
</div>

@include('emails.notifications.components.product-showcase', [
    'product' => $product,
    'imageUrl' => $imageUrl,
    'price' => $price,
])

@include('emails.notifications.components.cta-button', [
    'url' => $productUrl,
    'label' => 'Buy Now',
])

@include('emails.notifications.components.footer')
@endcomponent
