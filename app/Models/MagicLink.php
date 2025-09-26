<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class MagicLink extends Model
{use SoftDeletes;
    protected $fillable = ['user_id','token','expires_at','used'];
    protected $casts = ['expires_at' => 'datetime'];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
