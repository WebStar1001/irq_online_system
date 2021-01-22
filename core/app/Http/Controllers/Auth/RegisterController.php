<?php

namespace App\Http\Controllers\Auth;

use App\Currency;
use App\GeneralSetting;
use App\User;
use App\Http\Controllers\Controller;
use App\Wallet;
use App\WithdrawMethod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware(['guest']);
        $this->middleware('regStatus')->except('registrationNotAllowed');
    }

    public function showRegistrationForm()
    {
        $page_title = "Sign Up";
        return view(activeTemplate() . 'user.auth.register', compact('page_title'));
    }

    public function showRegistrationFormRef($username)
    {
        $ref_user = User::where('username', $username)->first();
        if (isset($ref_user)) {
            $page_title = "Sign Up";
            return view(activeTemplate() . 'user.auth.register', compact('page_title'));
        }
        return redirect()->route('user.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user = Validator::make($data, [
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'email' => 'required|string|email|max:160|unique:users',
            'mobile' => 'required|string|max:30|unique:users',
            'country' => 'required|string|max:80',
            'password' => 'required|string|min:6|confirmed',
            'username' => 'required|string|alpha_dash|unique:users|min:6',
            'company_name' => 'sometimes|required|string|max:191',
        ]);

        return $user;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $gnl = GeneralSetting::first();
        $currency = Currency::whereStatus(1)->get();

        if (isset($data['company_name'])){
            $merchant  = 2;
            $company_name = $data['company_name'];
        }else{
            $merchant  = 0;
            $company_name =null;
        }

        $user =  User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'company_name' => $company_name,
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'username' => trim(strtolower($data['username'])),
            'mobile' => $data['mobile'],
            'address' => [
                'address' => null,
                'state' =>  null,
                'zip' =>  null,
                'country' => $data['country'],
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
        return $user;
    }

    public function registered() 
    {
        return redirect()->route('user.home');
    }

}
