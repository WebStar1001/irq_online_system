<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Trx;
use App\User;
use App\UserApiKey;
use App\UserLogin;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Image;
use File;

class AuthenticateUserController extends Controller
{

    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }

    public function sendMailCode()
    {
        $user = Auth()->user();
        if ($this->checkValidCode($user, $user->ver_code, 2)) {
            $target_time = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            $delay = gmdate('i:s', $delay);
            $response = ['errors' => ['Please Try after ' . $delay . ' Seconds']];
            return response($response, 422);
        }
        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }
        $info = json_decode(json_encode(getIpInfo()), true);
        send_email($user, 'ACCOUNT_RECOVERY_CODE', [
            'code' => $user->ver_code,
            'ip' => request()->ip(),
            'browser' => $info['browser'],
            'time' => date('d M, Y h:i:s A'),
        ]);
        $response['success'] = "Email verification code sent successfully";
        return response($response, 200);
    }

    public function mailVerify(Request $request)
    {
        $rules = ['email_verified_code' => 'required|max:6'];
        $message = ['email_verified_code.required' => 'Email verification code is required'];

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }
        $user = Auth::user();
        if ($this->checkValidCode($user, $request->email_verified_code)) {
            $user->ev = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $response['success'] = "Email Verification successful";
            return response($response, 200);
        }
        $response = ['errors' => ["Verification Code Did not matched"]];
        return response($response);
    }

    public function sendSmsCode()
    {
        $user = Auth()->user();
        if ($this->checkValidCode($user, $user->ver_code, 2)) {
            $target_time = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            $delay = gmdate('i:s', $delay);
            $response = ['errors' => ['Please Try after ' . $delay . ' Seconds']];
            return response($response, 422);
        }

        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }
        $info = json_decode(json_encode(getIpInfo()), true);

        send_sms($user, 'ACCOUNT_RECOVERY_CODE', [
            'code' => $user->ver_code,
            'ip' => request()->ip(),
            'browser' => $info['browser'],
            'time' => date('d M, Y h:i:s A'),
        ]);
        $response['success'] = "SMS verification code sent successfully";
        return response($response, 200);
    }

    public function smsVerify(Request $request)
    {
        $rules = ['sms_verified_code' => 'required|max:6'];
        $message = ['sms_verified_code.required' => 'SMS verification code is required'];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }
        $user = Auth::user();
        if ($this->checkValidCode($user, $request->sms_verified_code)) {
            $user->sv = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $response['success'] = "Mobile Verification successful";
            return response($response, 200);
        }
        $response = ['errors' => ["Verification Code Did not matched"]];
        return response($response);
    }

    public function index()
    {
        $myWallet = Wallet::with('currency')->where('user_id', Auth::id())->get()->map(function ($item) {
            return [
                'currency' => $item->currency->name,
                'amount' => formatter_money($item->amount),
                'currency_code' => $item->currency->code,
            ];
        });

        $transactions = Trx::where('user_id', Auth::id())->latest()->limit(15)->get()->map(function ($data) {
            return [
                'M' => date('M', strtotime($data->created_at)),
                'd' => date('d', strtotime($data->created_at)),
                'remark' => $data->remark,
                'title' => $data->title,
                'type' => $data->type,
                'trx_amount' => formatter_money($data->amount) . ' ' . $data->currency->code,
                'Transaction ID' => $data->trx,
                'Charge' => formatter_money($data->charge) . ' ' . $data->currency->code,
                'Remaining Balance' => formatter_money($data->main_amo) . ' ' . $data->currency->code,
                'Details' => $data->title,
                'date' => date('d M, Y', strtotime($data->created_at)),
                'time' => date('h:i A', strtotime($data->created_at)),
            ];
        });


        $response = ['myWallet' => $myWallet, 'transactions' => $transactions];

        return response($response, 200);
    }


    public function transaction()
    {
        $transactions = Trx::where('user_id', Auth::id())->latest()->paginate(15);
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            return [
                'M' => date('M', strtotime($data->created_at)),
                'd' => date('d', strtotime($data->created_at)),
                'remark' => $data->remark,
                'title' => $data->title,
                'type' => $data->type,
                'trx_amount' => formatter_money($data->amount) . ' ' . $data->currency->code,
                'Transaction ID' => $data->trx,
                'Charge' => formatter_money($data->charge) . ' ' . $data->currency->code,
                'Remaining Balance' => formatter_money($data->main_amo) . ' ' . $data->currency->code,
                'Details' => $data->title,
                'date' => date('d M, Y', strtotime($data->created_at)),
                'time' => date('h:i A', strtotime($data->created_at)),
            ];
        });


        return response($transactions, 200);
    }

    public function transactionSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }


        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = $currency . ' : ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));

        $currencyList = Currency::where('status', 1)->get();

        $query = Trx::query();
        $query->with('user', 'currency')
            ->when($currency, function ($q, $currency) {
                $q->whereHas('currency', function ($curr) use ($currency) {
                    $curr->where('code', $currency);
                });
            })
            ->when($start_date, function ($q, $start_date) {
                $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
            })
            ->when($end_date, function ($q, $end_date) {
                $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
            });
        $transactions = $query->paginate(15);

        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            return [
                'M' => date('M', strtotime($data->created_at)),
                'd' => date('d', strtotime($data->created_at)),
                'remark' => $data->remark,
                'title' => $data->title,
                'type' => $data->type,
                'trx_amount' => formatter_money($data->amount) . ' ' . $data->currency->code,
                'Transaction ID' => $data->trx,
                'Charge' => formatter_money($data->charge) . ' ' . $data->currency->code,
                'Remaining Balance' => formatter_money($data->main_amo) . ' ' . $data->currency->code,
                'Details' => $data->title,
                'date' => date('d M, Y', strtotime($data->created_at)),
                'time' => date('h:i A', strtotime($data->created_at)),
            ];
        });

        $response = [
            'page_title' => $page_title,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'select_currency' => $currency,
            'currencyList' => $currencyList,
            'transactions' => $transactions
        ];


        return response($response, 200);
    }

    public function checkValidUser(Request $request)
    {
        $user_data = User::where('status', 1)->where('id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower(trim($request->search)))
                    ->orWhere('email', strtolower(trim($request->search)))
                    ->orWhere('mobile', trim($request->search));
            })
            ->first();

        if ($user_data) {
            $data['user'] = $user_data;
            $data['result'] = 'success';
        } else {
            $data['user'] = null;
            $data['result'] = 'error';
        }
        return response($data, 200);
    }

    public function user()
    {
        
        if(Auth::user()->image != null){
                $response['profile'] = get_image(config('constants.user.profile.path') .'/'.Auth::user()->image);
        }else{
                $response['profile'] = null;
        }
            
        $response['user'] = Auth::user();
        return response($response);
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'company_name' => 'sometimes|required|string|max:50',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'required|string|min:10|unique:users,mobile,' . $user->id,
            'address' => 'required|string|max:80',
            'state' => 'required|string|max:80',
            'zip' => 'required|string|max:40',
            'city' => 'required|string|max:50',
            'country' => 'required|string|max:50',
            'image' => 'sometimes|mimes:png,jpg,jpeg'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }
        $in['company_name'] = $request->company_name;
        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        $in['email'] = $request->email;
        $in['mobile'] = $request->mobile;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
            'city' => $request->city,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '.jpg';
            $location = 'assets/images/user/profile/' . $filename;
            $in['image'] = $filename;

            $path = './assets/images/user/profile/';
            $link = $path . $user->image;
            if (file_exists($link)) {
                @unlink($link);
            }
            Image::make($image)->save($location);
        }
        $user->fill($in)->save();

        $response['success'] = 'Profile Updated successfully.';
        $response['result'] = true;
        
        $response['user'] = $user;
        
        if($user->image != null){
            $response['profile'] = get_image(config('constants.user.profile.path') .'/'.$user->image);
        }else{
            $response['profile'] = null;
        }
        
        return response($response, 200);
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        try {

            $c_password = Auth::user()->password;
            $c_id = Auth::user()->id;
            $user = User::findOrFail($c_id);
            if (Hash::check($request->current_password, $c_password)) {
                $user->password = Hash::make($request->password);
                $user->save();

                $response['success'] = 'Password Changes successfully.';
                $response['result'] = true;
                return response($response);

            } else {
                $response = ['errors' => 'Current password not match.'];
                return response($response);
            }

        } catch (\PDOException $e) {
            $response = ['errors' => $e->getMessage()];
            return response($response);
        }
    }


    public function loginHistory()
    {
        $logs = UserLogin::where('user_id', Auth::id())->latest()->limit(10)->get()->map(function ($data) {
            return [
                'ip' => $data->user_ip,
                'browser' => $data->browser,
                'time' => date('d M, Y h:i A', strtotime($data->created_at)),
            ];
        });
        return response($logs, 200);
    }

    public function apiKey()
    {
        $user = User::findOrFail(Auth::id());
        $logs = $user->api_keys()->get()->map(function ($data) {
            return [
                'id' => $data->id,
                'name' => $data->name,
                'public_key' => $data->public_key,
                'secret_key' => $data->secret_key,
            ];
        });
        return response($logs, 200);
    }

    public function apiKeyStore(Request $request)
    {
        $rules = [
            'name' => 'required|max:30|alpha_dash'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all()]);
        }

        $check = UserApiKey::where('user_id',Auth::id())->where('name',$request->name)->first();
        if($check){
            $response = ['errors' => 'Already exist '. $request->name. ' credential'];
            return response($response);
        }

        $u['user_id'] = Auth::id();
        $u['name'] = $request->name;
        $u['public_key'] = getTrx(20);
        $u['secret_key'] = getTrx(16);
        UserApiKey::create($u);

        $response['success'] = 'API Key Generate Successfully';
        $response['result'] = true;
        return response($response);
    }

    public function apiKeyDelete(Request $request)
    {
        $rules = ['id' => 'required'];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all()]);
        }
        $data = UserApiKey::where('id', $request->id)->where('user_id',Auth::id())->first();
        if(!$data){
            $response = ['errors' => 'Invalid Request'];
            return response($response);
        }

        $data->delete();

        $response['success'] = 'API Key Remove Successfully';
        $response['result'] = true;
        return response($response);
    }



}
