<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function syncRelations(Product $product, string $relation, array $ids): void;
    public function createSeoMeta(Product $product, array $data): void;
    public function updateSeoMeta(Product $product, array $data): void;
    public function createVariants(Product $product, array $variants): void;
}
