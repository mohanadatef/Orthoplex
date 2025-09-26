<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','action','ip_address','user_agent','metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
