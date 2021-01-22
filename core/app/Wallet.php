<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;
    protected $table = "wallets";
    protected $guarded = [];


    public function currency()
    {
        return $this->belongsTo('App\Currency','wallet_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }


}
