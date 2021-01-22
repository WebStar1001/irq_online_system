<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeMoney extends Model
{
    protected $table = "exchange_money";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function from_currency()
    {
        return $this->belongsTo('App\Currency','from_currency_id','id');
    }
    public function to_currency()
    {
        return $this->belongsTo('App\Currency','to_currency_id','id');
    }


}
