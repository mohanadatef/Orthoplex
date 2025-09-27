<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrgApiKey extends Model
{
    protected $fillable = ['org_id','name','key','scopes','last_used_at','revoked_at'];
    protected $casts = ['scopes'=>'array','last_used_at'=>'datetime','revoked_at'=>'datetime'];
}
