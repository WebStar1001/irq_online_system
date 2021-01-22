<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected  $table = "vouchers";

    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id','id');
    }
    public function creator()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','use_id','id');
    }

}
