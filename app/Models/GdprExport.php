<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprExport extends Model
{
    protected $fillable = [
        'user_id','disk','path','token','expires_at','downloaded_at',
    ];

    protected $casts = [
        'expires_at'    => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
