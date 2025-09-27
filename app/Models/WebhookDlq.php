<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDlq extends Model
{
    protected $table = 'webhook_dlq';

    protected $fillable = [
        'webhook_id',
        'url',
        'event',
        'payload',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
