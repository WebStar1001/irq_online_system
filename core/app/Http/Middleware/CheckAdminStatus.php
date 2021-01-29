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
        $user = Auth::guard('admin')->user();
        if(Auth::guard('admin')->user()->status == 0)
        {
            Auth::guard('admin')->logout();
            $notify[] = ['error', 'Your Account has been Blocked!'];
            return redirect()->route('admin.login')->withNotify($notify);
        }else{
            if ($user->tv) {
                return $next($request);
            } else {
                return redirect()->route('admin.authorization');
            }
        }

        return $next($request);

    }
}
