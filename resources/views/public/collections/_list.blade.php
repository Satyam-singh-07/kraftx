@php
    $fallbackCategoryImages = [
        'cate-1.jpg',
        'cate-2.jpg',
        'cate-3.jpg',
        'cate-4.jpg',
        'cate-5.jpg',
        'cate-6.jpg',
    ];
@endphp

@foreach ($collections as $collection)
    <div class="col-4 col-md-3 col-lg-2 wow fadeInUp" data-wow-delay="{{ ($loop->index % 6) * 0.1 }}s">
        <a href="{{ route('collection.show', $collection->slug) }}" class="collection-item-v2 hover-img">
            <div class="collection-image img-style rounded-circle overflow-hidden mb-10" style="aspect-ratio: 1/1;">
                <img loading="lazy" 
                    src="{{ $collection->image ? Storage::url($collection->image) : asset('assets/images/category/' . $fallbackCategoryImages[$loop->index % count($fallbackCategoryImages)]) }}" 
                    alt="{{ $collection->name }}"
                    class="rounded-circle object-fit-cover w-100 h-100">
            </div>
            <p class="collection-title text-center text-caption-1 fw-6 cl-text-2">{{ $collection->name }}</p>
        </a>
    </div>
@endforeach
