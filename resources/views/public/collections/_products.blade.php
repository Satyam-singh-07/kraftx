@foreach($products as $product)
    <div class="collection-product-item col-6 col-md-4 col-lg-3">
        <x-product-card :product="$product" />
    </div>
@endforeach
