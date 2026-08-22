<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator;

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model;

    public function create(array $attributes): Model;

    public function update(int|Model $idOrModel, array $attributes): Model;

    public function delete(int|Model $idOrModel): bool;

    public function searchableFields(): array;

    public function sortableFields(): array;
}
