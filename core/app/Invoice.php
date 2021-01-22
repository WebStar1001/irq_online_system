<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $table = "invoices";
    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }



}
