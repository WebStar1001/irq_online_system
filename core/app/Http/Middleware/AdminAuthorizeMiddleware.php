<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AdminAuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $access = null)
    {
        $admin =  Auth::guard('admin')->user();

        $userAccess = json_decode($admin->access);
        if(in_array($access, $userAccess)){
            return $next($request);
        }
        abort(403);
    }
}
