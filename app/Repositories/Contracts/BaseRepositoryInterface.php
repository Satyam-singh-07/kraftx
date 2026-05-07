<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []): Collection;

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    public function findBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;
}
