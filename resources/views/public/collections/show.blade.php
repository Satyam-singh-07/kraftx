<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $collection->name }} - Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $collection->name }}</h1>
        @if($collection->description)
            <p class="text-gray-600 mb-8">{{ $collection->description }}</p>
        @endif
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <div class="h-48 bg-gray-100 flex items-center justify-center p-4">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="max-h-full object-contain">
                            @else
                                <span class="text-gray-400">No Image</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-blue-600 font-bold">${{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                                @if($product->sale_price)
                                    <span class="text-gray-400 line-through text-sm">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    No products found in this collection.
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
    
</body>
</html>
