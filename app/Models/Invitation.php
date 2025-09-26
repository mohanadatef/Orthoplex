<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitation extends Model {
    use SoftDeletes;
    protected $fillable = ['org_id','email','token','accepted_at'];
    protected $dates = ['accepted_at'];

    public function org() {
        return $this->belongsTo(Org::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'email','email');
    }
}
