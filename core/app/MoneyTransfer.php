<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoneyTransfer extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "money_transfers";


    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id');
    }

    public function sender()
    {
        return $this->belongsTo('App\User','sender_id','id');
    }
    public function receiver()
    {
        return $this->belongsTo('App\User','receiver_id','id');
    }

}
