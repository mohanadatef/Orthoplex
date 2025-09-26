<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model {
    use SoftDeletes;
    protected $fillable = ['url','event','payload','status','last_error','attempts','next_attempt_at'];
    protected $casts = ['payload' => 'array', 'next_attempt_at' => 'datetime'];
}
