<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $fillable = ['key','user_id','endpoint','method','request_hash','response_body','status_code'];
    protected $casts = ['response_body' => 'array'];
}
