<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:50',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $user = User::where('username', trim(strtolower($request->username)))->first();
        if(!$user){
            $response = ['errors'=>['User does not exist']];
            return response($response,422);
        }

        if (Hash::check($request->password, $user->password)) {
            if($user->status == 0){
                $response = ['errors'=>['Account Has been Suspended']];
                return response($response,422);
            }

            $info = json_decode(json_encode(getIpInfo()), true);
            $ul['user_id'] = $user->id;
            $ul['user_ip'] =  request()->ip();
            $ul['longitude'] =  implode(',',$info['long']);
            $ul['lat'] =  implode(',',$info['lat']);
            $ul['location'] =  implode(',',$info['city']) . (" - ". implode(',',$info['area']) ."- ") . implode(',',$info['country']) . (" - ". implode(',',$info['code']) . " ");
            $ul['country_code'] = implode(',',$info['code']);
            $ul['browser'] = $info['browser'];
            $ul['os'] = $info['os_platform'];
            $ul['country'] =  implode(',', $info['country']);
            UserLogin::create($ul);

            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            
            if($user->image != null){
                $profile = get_image(config('constants.user.profile.path') .'/'.$user->image);
            }else{
                $profile = null;
            }
            
            
            $response = ['token' => $token, 'user' => $user, 'profile' =>$profile];

            return response($response, 200);
        } else {
            $response = ['errors'=>['Incorrect Password']];
            return response($response,422);
        }

    }
}
