<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Trx;
use App\Wallet;
use App\Withdrawal;
use App\WithdrawMethod;
use Illuminate\Http\Request;
use Auth;
use File;
use Image;
use Validator;

class UserWithdrawMoneyController extends Controller
{
    public function withdrawMoney()
    {
        $basic = GeneralSetting::first();
        if($basic->withdraw_status == 0){
            return response(['errors' => 'Withdraw Money DeActive By Admin']);
        }
        $response['currency'] = Currency::whereStatus(1)->get();
        $response['withdrawMethod'] = WithdrawMethod::whereStatus(1)->get()->map(function ($data){
            return [
                "id" => $data->id,
                "name" => $data->name,
                "min_limit" => formatter_money($data->min_limit),
                "max_limit" => formatter_money($data->max_limit),
                "delay" => $data->delay,
                "fixed_charge" => formatter_money($data->fixed_charge),
                "percent_charge" => formatter_money($data->percent_charge),
                "rate" => $data->rate,
                "image" => get_image(config('constants.withdraw.method.path').'/'. $data->image),
                "currency" => $data->currency,
                "user_data" => $data->user_data
            ];
        });
        return response($response,200);
    }

    public function withdrawMoneyRequest(Request $request)
    {

        $rules = [
            'method_code' => 'required',
            'amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'currency_id' => 'required',
            'currency' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }


        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->first();
        if(!$method){
            return response(['errors' => 'Invalid Method.']);
        }

        $walletCurr = Currency::where('id', $request->currency_id)->where('status', 1)->first();
        if(!$walletCurr){
            return response(['errors' => 'Invalid Currency.']);
        }


        $authWallet = Wallet::where('wallet_id', $walletCurr->id)->where('user_id', Auth::id())->first();
        if(!$authWallet){
            return response(['errors' => 'Invalid Wallet.']);
        }


        $amountInCur = ($request->amount / $walletCurr->rate) * $method->rate;
        $charge = $method->fixed_charge + ($amountInCur * $method->percent_charge / 100);
        $finalAmo = $amountInCur - $charge;


        if ($finalAmo < $method->min_limit) {
            return response(['errors' => 'Your Request Amount is Smaller Then Withdraw Minimum Amount.']);
        }
        if ($finalAmo > $method->max_limit) {
            return response(['errors' => 'Your Request Amount is Larger Then Withdraw Maximum Amount.']);
        }

        if (formatter_money($request->amount) > $authWallet->amount) {
            return response(['errors' => 'Your Request Amount is Larger Then Your Current Balance.']);
        } else {
            $w['method_id'] = $method->id; // wallet method ID
            $w['user_id'] = Auth::id();
            $w['wallet_id'] = $authWallet->id; // User Wallet ID
            $w['currency_id'] = $walletCurr->id; // Currency ID
            $w['amount'] = formatter_money($request->amount);
            $w['currency'] = $method->currency;
            $w['method_rate'] = $method->rate;
            $w['currency_rate'] = $walletCurr->rate;
            $w['charge'] = $charge;
            $w['wallet_charge'] = $charge* $walletCurr->rate;
            $w['final_amount'] = $finalAmo;
            $w['delay'] = $method->delay;

            $multiInput = [];
            if ($method->user_data != null) {
                foreach ($method->user_data as $k => $val) {
                    $multiInput[str_replace(' ', '_', $val)] = null;
                }
            }
            $w['detail'] = json_encode($multiInput);
            $w['trx'] = getTrx();
            $w['status'] = -1;

            $result = Withdrawal::create($w);

            $response['result'] = true;
            $response['url'] = route('withdraw.preview',$result->trx);
            return response($response);
        }
    }


    public function withdrawReqPreview($wtrx){

        $basic = GeneralSetting::first();
        if($basic->withdraw_status == 0){
            return response(['errors' => 'Withdraw Money DeActive By Admin']);
        }
        $withdraw = Withdrawal::with('method','curr','wallet')->where('trx',$wtrx)->where('status',-1)->latest()->first();
        if(!$withdraw){
            return response(['errors' => 'Invalid Request!']);
        }

        $response['trx'] = $wtrx;
        $response['current_balance'] = formatter_money($withdraw->wallet->amount) . ' '.$withdraw->curr->code;
        $response['request_amount'] = formatter_money($withdraw->amount ) . ' '.$withdraw->curr->code;
        $response['withdrawal_charge'] = formatter_money($withdraw->charge) . ' '.$withdraw->currency;
        $response['you_will_get'] = formatter_money($withdraw->final_amount) . ' '.$withdraw->currency;
        $response['available_balance'] = formatter_money($withdraw->wallet->amount - $withdraw->amount) . ' '.$withdraw->curr->code;

        $form = [];
        foreach(json_decode($withdraw->detail) as $k=> $value){
            $form_field = [
                'level' => str_replace('_',' ',$k),
                'field_name' => $k,
            ];
            array_push($form,$form_field);
        }
        $response['form'] = $form;
        return response($response,200);
    }


    public function withdrawReqSubmit(Request $request)
    {
        $rules = [
          'trx' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' =>$validator->errors()->all()],422);
        }
        $withdraw = Withdrawal::with('method','curr','wallet')->where('trx',$request->trx)->where('status',-1)->latest()->first();
        if(!$withdraw){
            return response(['errors' => 'Invalid Request.']);
        }
        $customField = [];
        foreach (json_decode($withdraw->detail) as $k => $val){
            $customField[$k] =  ['required'];
        }
        $validator = Validator::make($request->all(), $customField);
        if ($validator->fails()) {
            return response(['errors' =>$validator->errors()->all()],422);
        }

        $in = $request->except('_token');
        $multiInput = [];
        foreach ($in as $k => $val){
            $multiInput[$k] =  $val;
        }

        $authWallet  = Wallet::find($withdraw->wallet_id);

        if ($withdraw->amount > $authWallet->amount) {
            return response(['errors' => 'Your Request Amount is Larger Then Your Current Balance.']);
        } else {
            $withdraw->detail = json_encode($multiInput);
            $withdraw->status = 0;
            $withdraw->save();

            $authWallet->amount =  formatter_money($authWallet->amount - $withdraw->amount);
            $authWallet->update();

            Trx::create([
                'user_id' => $authWallet->user->id,
                'amount' => $withdraw->amount,
                'main_amo' => $authWallet->amount,
                'charge' => $withdraw->wallet_charge,
                'currency_id' => $authWallet->currency->id,
                'trx_type' => '-',
                'remark' => 'Withdraw Money ',
                'title' => formatter_money($withdraw->final_amount). ' '. $withdraw->currency .' Withdraw Via ' . $withdraw->method->name,
                'trx' => $withdraw->trx
            ]);
            notify($authWallet->user, $type = 'withdraw_request', [
                'amount' => formatter_money($withdraw->amount),
                'currency' => $withdraw->curr->code,
                'withdraw_method' => $withdraw->method->name,
                'method_amount' => formatter_money($withdraw->final_amount),
                'method_currency' => $withdraw->currency,
                'duration' => $withdraw->delay,
                'trx' => $withdraw->trx,
            ]);
            $response = ['success' => "Withdraw Request Successfully Send"];
            $response['result'] = true;
            return response($response);
        }
    }



    public function withdrawLog()
    {
        $logs = Withdrawal::where('user_id',Auth::id())->where('status','!=',-1)->latest()->paginate(20);
        $logs = resourcePaginate($logs, function ($data) use ($logs) {
            if($data->status == 0){
                $level = 'Pending';
            }elseif ($data->status == 1){
                $level = 'Completed';
            }elseif ($data->status == 2){
                $level = 'Rejected';
            }
            return [
                'transaction' => $data->trx,
                'gateway' => $data->method->name,
                'amount' => formatter_money($data->amount) . ' '.$data->curr->code,
                'date' =>date('d M, Y h:i:s A', strtotime($data->created_at)),
                'level' =>$level,
                'status' =>$data->status
            ];
        });
        return response($logs,200);
    }

}
