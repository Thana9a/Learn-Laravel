<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for all report persistence operations.
 * Decouples the Service/Job layer from Eloquent internals.
 */
interface ReportRepositoryInterface
{
    public function all(int $userId): Collection;

    public function findById(int $id): ?Report;

    public function create(array $attributes): Report;

    public function updateStatus(int $id, string $status, ?array $result = null, ?string $errorMessage = null): Report;

    public function stats(int $userId): array;
}
