<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\QueryHelpers;
use Illuminate\Contracts\Pagination\CursorPaginator;

class UserService
{
    use QueryHelpers;

    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function list(array $filters, ?string $include, ?string $fields, int $perPage = 15): array
    {
        $query = $this->repository->query();

        $this->applyFilters($query, $filters['filter'] ?? null);
        $this->applyIncludes($query, $include);

        $users = $query->orderBy('id')->cursorPaginate($perPage);

        $data = collect($users->items())->map(fn($user) => $user->toArray());
        $data = $this->applySparseFields($data, $fields);

        return [
            'data' => $data,
            'meta' => [
                'next_cursor' => $users->nextCursor()?->encode(),
                'prev_cursor' => $users->previousCursor()?->encode(),
            ]
        ];
    }

    public function delete(int $id): bool
    {
        $user = $this->repository->findById($id);
        return $user ? $this->repository->softDelete($user) : false;
    }

    public function restore(int $id): bool
    {
        $user = $this->repository->findById($id, true);
        return ($user && $user->trashed()) ? $this->repository->restore($user) : false;
    }

    public function updateOptimistic(User $user, array $data): bool
    {
        return $this->repository->updateOptimistic($user, $data);
    }
}
