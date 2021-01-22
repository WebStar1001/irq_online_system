<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAdminStatus
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
        if(Auth::guard('admin')->user()->status == 0)
        {
            Auth::guard('admin')->logout();
            $notify[] = ['error', 'Your Account has been Blocked!'];
            return redirect()->route('admin.login')->withNotify($notify);
        }

        return $next($request);

    }
}
