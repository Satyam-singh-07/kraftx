<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findBySlug(string $slug): ?Product;
    public function syncRelations(Product $product, string $relation, array $ids): void;
    public function createSeoMeta(Product $product, array $data): void;
    public function updateSeoMeta(Product $product, array $data): void;
    public function createVariants(Product $product, array $variants): void;
    public function search(string $query, int $limit = 10);
    public function getTrending(int $limit = 5);
}
