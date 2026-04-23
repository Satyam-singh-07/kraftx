<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = Collection::all();

        foreach ($collections as $collection) {
            for ($i = 1; $i <= 5; $i++) {
                $productName = "{$collection->name} Item {$i}";
                $price = rand(500, 5000);
                
                $product = Product::create([
                    'name' => $productName,
                    'slug' => Str::slug($productName) . '-' . uniqid(),
                    'short_description' => "This is a short description for {$productName}.",
                    'description' => "This is a detailed description for {$productName}. It belongs to the {$collection->name} collection.",
                    'price' => $price,
                    'sale_price' => rand(0, 1) ? $price * 0.8 : null,
                    'stock' => rand(10, 100),
                    'sku' => strtoupper(Str::random(10)),
                    'status' => true,
                    'featured' => rand(0, 1),
                    'is_trending' => rand(0, 1),
                ]);

                // Attach to collection
                $product->collections()->attach($collection->id);

                // Add a dummy primary image record (using a placeholder path that exists in public assets)
                $product->images()->create([
                    'image_path' => 'assets/images/product/product-' . rand(1, 8) . '.jpg',
                    'is_primary' => true,
                ]);

                // Add a secondary image for hover effect
                $product->images()->create([
                    'image_path' => 'assets/images/product/product-' . rand(1, 8) . '_2.jpg',
                    'is_primary' => false,
                ]);
            }
        }
    }
}
