<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RequestMoney extends Model
{
	use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = "request_moneys";

  	public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\User','receiver_id','id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','sender_id','id');
    }



}
