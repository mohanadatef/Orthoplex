<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Org extends Model {
    use SoftDeletes;
    protected $fillable = ['name','domain','settings','webhook_secret'];
    protected $casts = ['settings' => 'array'];

    public function users() {
        return $this->belongsToMany(User::class, 'org_user')->withTimestamps();
    }
}
