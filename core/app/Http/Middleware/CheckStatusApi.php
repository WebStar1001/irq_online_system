<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckStatusApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if((Auth::user()->sv == 1 )&& (Auth::user()->ev == 1) && (Auth::user()->status == 1))
        {
            return $next($request);
        }else{

            if(Auth::user()->ev == 0){
                $response = ['errors'=>['Email Verification Required']];
                return response($response,422);
            }elseif(Auth::user()->sv == 0){
                $response = ['errors'=>['Mobile Verification Required']];
                return response($response,422);
            }
        }
    }
}
