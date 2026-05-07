<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    public function findBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->where($column, $value)->first($columns);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }
}
