<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Return the model class name.
     */
    abstract public function model(): string;

    /**
     * Get a new instance of the model's query builder.
     */
    public function query(): Builder
    {
        $modelClass = $this->model();
        return $modelClass::query();
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $filters = []): LengthAwarePaginator
    {
        $query = $this->query();

        $this->applySearch($query, $filters['search'] ?? null);
        $this->applyFilters($query, $filters);
        $this->applySorting(
            $query,
            $filters['sort_by'] ?? 'created_at',
            $filters['sort_direction'] ?? 'desc'
        );

        return $query->paginate($perPage, $columns)->withQueryString();
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->query()->with($relations)->find($id, $columns);
    }

    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->query()->with($relations)->findOrFail($id, $columns);
    }

    public function create(array $attributes): Model
    {
        $modelClass = $this->model();
        return $modelClass::create($attributes);
    }

    public function update(int|Model $idOrModel, array $attributes): Model
    {
        $model = $idOrModel instanceof Model ? $idOrModel : $this->findOrFail($idOrModel);
        $model->update($attributes);
        return $model->fresh();
    }

    public function delete(int|Model $idOrModel): bool
    {
        $model = $idOrModel instanceof Model ? $idOrModel : $this->findOrFail($idOrModel);
        return (bool) $model->delete();
    }

    public function searchableFields(): array
    {
        return [];
    }

    public function sortableFields(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    protected function applySearch(Builder $query, ?string $search): void
    {
        if (empty($search) || empty($this->searchableFields())) {
            return;
        }

        $fields = $this->searchableFields();
        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        $query->where(function (Builder $q) use ($fields, $operator, $search) {
            foreach ($fields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, $operator, '%' . $search . '%');
                } else {
                    $q->orWhere($field, $operator, '%' . $search . '%');
                }
            }
        });
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Subclasses override to add custom filters
    }

    protected function applySorting(Builder $query, string $sortBy, string $direction): void
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, $this->sortableFields(), true)) {
            $query->orderBy($sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }
}
