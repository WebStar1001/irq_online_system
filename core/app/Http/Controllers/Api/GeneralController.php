<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function basic(){
        $basic = GeneralSetting::first();
        return response($basic,200);
    }

    public function currencyList(){
        $currencyList = Currency::where('status',1)->get();
        return response($currencyList,200);
    }
}
