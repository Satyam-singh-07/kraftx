<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['images', 'collections']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $query->where('featured', $filters['featured']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_low_high' => $query->orderBy('price', 'asc'),
            'price_high_low' => $query->orderBy('price', 'desc'),
            'popularity' => $query->orderBy('is_trending', 'desc')->latest(),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->with(['images', 'collections', 'tags', 'variants', 'seoMeta'])
            ->where('slug', $slug)
            ->where('status', true)
            ->first();
    }

    public function syncRelations(Product $product, string $relation, array $ids): void
    {
        $product->$relation()->sync($ids);
    }

    public function createSeoMeta(Product $product, array $data): void
    {
        if (!empty($data)) {
            $product->seoMeta()->create($data);
        }
    }

    public function updateSeoMeta(Product $product, array $data): void
    {
        if (!empty($data)) {
            $product->seoMeta()->updateOrCreate(
                ['metaable_id' => $product->id, 'metaable_type' => Product::class],
                $data
            );
        }
    }

    public function createVariants(Product $product, array $variants): void
    {
        $product->variants()->delete(); // Reset existing
        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }
    }

    public function search(string $query, int $limit = 10)
    {
        return $this->model->with(['images', 'variants'])
            ->where('status', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();
    }

    public function getTrending(int $limit = 5)
    {
        return $this->model->with(['images', 'variants'])
            ->where('status', true)
            ->where('is_trending', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
