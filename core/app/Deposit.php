<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';
    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }
    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id','id');
    }

    public function express_payment()
    {
        return $this->belongsTo('App\ExpressPayment','api_id','id');
    }


    public function invoice_payment()
    {
        return $this->belongsTo('App\Invoice','invoice_id','id');
    }



    // scope
    public function scopeGateway_currency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function scopeBaseCurrency()
    {
        return $this->gateway->crypto == 1 ? 'USD' : $this->method_currency;
    }


}
