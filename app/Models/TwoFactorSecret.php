<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TwoFactorSecret extends Model {
    use SoftDeletes;
    protected $fillable = ['name','key','scopes','expires_at','rotated_at','revoked'];
    protected $casts = ['scopes' => 'array', 'expires_at' => 'datetime', 'rotated_at' => 'datetime', 'revoked' => 'boolean'];

    public static function generateKey() {
        return 'sk_' . Str::random(40);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
