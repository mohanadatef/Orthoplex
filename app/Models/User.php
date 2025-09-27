<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasRoles, Searchable;

    protected $fillable = [
        'name', 'email', 'password', 'org_id', 'email_verified_at', 'last_login_at', 'login_count', 'version',
        'two_factor_secret', 'two_factor_enabled'
    ];

    protected $hidden = ['password', 'two_factor_secret'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean'
    ];

    public function org()
    {
        return $this->belongsTo(Org::class);
    }
}


public function toSearchableArray(): array
{
    return ['id'=>$this->id,'name'=>$this->name,'email'=>$this->email,'org_id'=>$this->org_id];
}
