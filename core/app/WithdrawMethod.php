<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{

    protected  $table = "withdraw_methods";
    protected $guarded = ['id'];

    protected $casts = [
        'user_data' => 'object',
    ];

    public function method(){
        return $this->belongsTo('App\Withdrawal','method_id','id');
    }


    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public function currency(){
        return $this->belongsTo('App\Currency','currency_id','id');
    }




}
