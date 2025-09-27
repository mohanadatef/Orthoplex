<?php

namespace App\Services;

use App\DTOs\DeleteRequestDTO;
use App\Repositories\Contracts\DeleteRequestRepositoryInterface;
use App\Jobs\ExportUserDataJob;
use App\Jobs\DeleteUserDataJob;
use App\Notifications\DeleteRequestApprovedNotification;
use App\Notifications\DeleteRequestRejectedNotification;
use Illuminate\Support\Facades\Auth;

class GDPRService
{
    public function __construct(
        private readonly DeleteRequestRepositoryInterface $repository
    ) {}

    public function exportUserData(): void
    {
        ExportUserDataJob::dispatch(Auth::id());
    }

    public function requestDelete(DeleteRequestDTO $dto)
    {
        return $this->repository->create([
            'user_id' => $dto->user_id,
            'status'  => $dto->status,
            'reason'  => $dto->reason
        ]);
    }

    public function approve(int $id)
    {
        $req = $this->repository->findById($id);
        if (!$req || $req->status !== 'pending') {
            return null;
        }

        $req->status = 'approved';
        $req->approved_by = Auth::id();
        $req->approved_at = now();
        $req->save();

        DeleteUserDataJob::dispatch($req->user);
        $req->user->notify(new DeleteRequestApprovedNotification());

        return $req;
    }

    public function reject(int $id)
    {
        $req = $this->repository->findById($id);
        if (!$req || $req->status !== 'pending') {
            return null;
        }

        $req->status = 'rejected';
        $req->approved_by = Auth::id();
        $req->approved_at = now();
        $req->save();

        $req->user->notify(new DeleteRequestRejectedNotification());

        return $req;
    }
}
