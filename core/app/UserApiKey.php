<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserApiKey extends Model
{
    protected $guarded = [];
    protected $table = "user_api_keys";

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }


}
