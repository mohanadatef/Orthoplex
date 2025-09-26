<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginEvent extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','ip','user_agent','occurred_at'];
    protected $dates = ['occurred_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
