<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;


class User extends Authenticatable implements JWTSubject , MustVerifyEmail
{
    use Notifiable, HasRoles,MustVerifyEmailTrait,SoftDeletes;

    protected $fillable = ['name','email','password','locale','active'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'active' => 'boolean'];
    protected $dates = ['deleted_at'];
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }

    public function orgs() {
        return $this->belongsToMany(Org::class, 'org_user')->withTimestamps()->withPivot('role');
    }
    public function loginEvents() {
        return $this->hasMany(LoginEvent::class);
    }

    public function twoFactorSecret()
    {
        return $this->hasOne(TwoFactorSecret::class);
    }

    public function twoFactorBackupCodes()
    {
        return $this->hasMany(TwoFactorBackupCode::class);
    }
}
