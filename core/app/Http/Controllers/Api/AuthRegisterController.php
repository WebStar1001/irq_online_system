<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\User;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthRegisterController extends Controller
{
    public function register(Request $request)
    {
        $gnl = GeneralSetting::first();
        $currency = Currency::whereStatus(1)->get();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'username' => 'required|string|alpha_dash|unique:users|min:6',
            'email' => 'required|string|email|max:160|unique:users',
            'mobile' => 'required|string|max:30|unique:users',
            'country' => 'required|string|max:80',
            'password' => 'required|string|min:6|confirmed',
            'company_name' => 'sometimes|required|string|max:191',
        ]);

        if($validator->fails()){
            return response(['errors' =>$validator->errors()->all()],422);
        }

        if (isset($data['company_name'])){
            $merchant  = 2;
            $company_name = $request->company_name;
        }else{
            $merchant  = 0;
            $company_name = null;
        }

        $user =  User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'company_name' => $company_name,
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'username' => trim(strtolower($request->username)),
            'mobile' => $request->mobile,
            'address' => [
                'address' => null,
                'state' =>  null,
                'zip' =>  null,
                'country' => $request->country,
                'city' =>  null,
            ],
            'status' => 1,
            'ev' =>  $gnl->ev ? 0 : 1,
            'sv' =>  $gnl->sv ? 0 : 1,
            'ts' => 0,
            'tv' => 1,
            'merchant' => $merchant,
        ]);

        foreach ($currency  as $data)
        {
            $wallet['user_id'] = $user->id;
            $wallet['wallet_id'] = $data->id;
            $wallet['amount'] = 0;
            $wallet['status'] = 1;
            Wallet::create($wallet);
        }

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;


        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }


        if ($gnl->ev == 1) {
            send_email($user, 'EVER_CODE', [
                'code' => $user->ver_code
            ]);
        }
        if ($gnl->sv == 1) {
            send_sms($user, 'SVER_CODE', [
                'code' => $user->ver_code
            ]);
        }


        return response()->json(['user' => $user, 'token' => $token], 200);

    }


    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }
}
