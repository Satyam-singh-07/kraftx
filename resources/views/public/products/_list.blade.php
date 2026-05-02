@foreach ($products as $product)
    <div class="col-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="{{ ($loop->index % 4) * 0.1 }}s">
        <x-product-card :product="$product" />
    </div>
@endforeach
