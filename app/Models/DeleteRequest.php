<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeleteRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'org_id',
        'status',
        'approver_id',
        'approved_at',
        'reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function approve(User $approver, string $reason = null): void
    {
        $this->update([
            'status' => 'approved',
            'approver_id' => $approver->id,
            'approved_at' => now(),
            'reason' => $reason,
        ]);
    }

    public function reject(User $approver, string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approver_id' => $approver->id,
            'approved_at' => now(),
            'reason' => $reason,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
