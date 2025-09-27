<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GdprExport extends Model
{
    protected $fillable = ['user_id','token','path','available_until','downloaded_at'];
    protected $casts = ['available_until'=>'datetime','downloaded_at'=>'datetime'];
}
