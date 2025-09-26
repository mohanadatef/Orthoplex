<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TwoFactorBackupCode extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','code','used'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
