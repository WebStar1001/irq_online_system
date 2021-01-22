<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpressPayment extends Model
{
    protected $table = "express_payments";
    protected $guarded = [];

    public function wallet()
    {
        return $this->belongsTo('App\Wallet','wallet_id','id');
    }


    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id','id');
    }

    public function merchant()
    {
        return $this->belongsTo('App\User','payto_id','id');
    }
}
