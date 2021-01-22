<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Session;
use Auth;

class ApiController extends Controller
{
    public function paynowSignin(Request $request)
    {
        $rules = ['username' => 'required', 'password' => 'required'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['fail' => true, 'errors' => $validator->errors()]);
        $data['page_title'] = "Payment Preview";

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            $track = Session::get('dataPayment');
            $url = route('api.preview.confirm',encrypt($track));
            return response()->json(['url' => $url, 'status'=>'authenticate']);
        } else {
            return response()->json(['msg' => "Username or Password Don't match", 'status'=>'credential']);
        }
    }


}
