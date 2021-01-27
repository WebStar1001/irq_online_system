<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Lib\GoogleAuthenticator;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }

    public function authorizeForm()
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $page_title = 'Google Authenticator';
            return view('admin.auth.authorize', compact('user', 'page_title'));
        }
        return redirect()->route('admin.login');
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->guard('admin')->user();

        $this->validate(
            $request, [
                'code' => 'required',
            ]
        );
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;
        if ($oneCode == $oneCode) {
            $user->tv = 1;
            $user->save();
            return redirect()->route('admin.dashboard');
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }
}
