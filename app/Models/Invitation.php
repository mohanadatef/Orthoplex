<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['org_id','email','token','expires_at','accepted_at'];
    protected $casts = ['expires_at'=>'datetime','accepted_at'=>'datetime'];
}
