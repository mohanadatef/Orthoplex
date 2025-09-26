<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','key','secret','expires_at','grace_until'];
    protected $casts = [
        'expires_at' => 'datetime',
        'grace_until' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function inGracePeriod(): bool
    {
        return $this->grace_until && Carbon::now()->lessThanOrEqualTo($this->grace_until);
    }
}
