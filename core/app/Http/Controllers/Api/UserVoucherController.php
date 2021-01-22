<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Trx;
use App\Voucher;
use App\Wallet;
use Illuminate\Http\Request;
use Auth;
use Validator;

class UserVoucherController extends Controller
{
    public function vouchers()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            $response = ['errors' => ["Voucher DeActive By Admin"]];
            return response($response);
        }
        $transactions = Voucher::where('user_id', Auth::id())->latest()->paginate(15);
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            if ($data->status == 0) {
                $level = "Pending";
            } elseif ($data->status == 1) {
                $level = "Used";
            }
            return [
                'id' => $data->id,
                'date' => date('d M, Y', strtotime($data->created_at)),
                'code' => $data->code,
                'amount' => formatter_money($data->amount) . " " . $data->currency->code,
                'level' => $level,
                'status' => $data->status,
            ];
        });
        return response($transactions, 200);
    }

    public function voucherActiveCodePreview(Request $request)
    {
        $rules = [
            'code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $voucher = Voucher::with('currency')->where('code', trim($request->code))->first();
        if (!$voucher) {
            $response = ['errors' => ["You Entered Invalid Code!"]];
            return response($response);
        }

        if ($voucher->status != 0) {
            $response = ['errors' => ["This Code Already Used!"]];
            return response($response);
        }

        $basic = GeneralSetting::first();
        $voucherActive = json_decode($basic->voucher, false);
        $percentCharge = $voucherActive->active_voucher->percent_charge;
        $fixedCharge = $voucherActive->active_voucher->fix_charge;


        $data['code'] = $voucher->currency->code;
        $data['currency'] = $voucher->currency->id;
        $data['charge'] = formatter_money((($voucher->amount * $percentCharge) / 100) + ($fixedCharge * $voucher->currency->rate));
        $data['amount'] = $voucher->amount;
        $data['voucher'] = $voucher->code;
        $data['percentCharge'] = $percentCharge;
        $data['fixedCharge'] = $fixedCharge;

        $data['template'] = [
            'amount' => formatter_money($voucher->amount) . ' ' . $voucher->currency->code,
            'percent_charge' => formatter_money($percentCharge) . ' %',
            'fixed_charge' => formatter_money($fixedCharge) . ' ' . $voucher->currency->code,
            'final_charge' => formatter_money((($voucher->amount * $percentCharge) / 100) + ($fixedCharge * $voucher->currency->rate)) . ' ' . $voucher->currency->code,
            'you_got' => formatter_money($voucher->amount - $data['charge']) . ' ' . $voucher->currency->code,
        ];
        return response($data, 200);
    }


    public function voucherSaveCode(Request $request)
    {
        $rules = [
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $voucher = Voucher::with('currency', 'creator', 'user')->where('code', $request->code)->first();
        if (!$voucher) {
            $response = ['errors' => ["You Entered Invalid Code!"]];
            return response($response);
        }

        if ($voucher->status != 0) {
            $response = ['errors' => ["This Code Already Used!"]];
            return response($response);
        }

        $basic = GeneralSetting::first();
        $auth = Auth::user();

        $voucherActive = json_decode($basic->voucher, false);
        $percentCharge = $voucherActive->active_voucher->percent_charge;
        $fixedCharge = $voucherActive->active_voucher->fix_charge;

        $charge = formatter_money((($voucher->amount * $percentCharge) / 100) + ($fixedCharge * $voucher->currency->rate));

        $amount = $voucher->amount - $charge;

        $authWallet = Wallet::with('currency', 'user')->where('user_id', $auth->id)->where('wallet_id', $voucher->currency_id)->first();
        if (!$authWallet) {
            $response = ['errors' => ["Invalid Wallet!"]];
            return response($response);
        }

        $authWallet->amount = formatter_money($authWallet->amount + $amount);
        $authWallet->save();

        $voucher->use_id = Auth::id();
        $voucher->use_charge = $charge;
        $voucher->useable_amount = $amount;
        $voucher->status = 1;
        $voucher->save();

        $trx = getTrx();
        Trx::create([
            'user_id' => $auth->id,
            'amount' => formatter_money($amount),
            'main_amo' => formatter_money($authWallet->amount),
            'charge' => $charge,
            'currency_id' => $voucher->currency->id,
            'trx_type' => '+',
            'remark' => 'Voucher Activated',
            'title' => 'Voucher Activated Successfully',
            'trx' => $trx
        ]);

        notify($authWallet->user, $type = 'voucher_redeem', [
            'amount' => formatter_money($amount),
            'charge' => formatter_money($charge),
            'total' => formatter_money($voucher->amount),
            'currency' => $authWallet->currency->code,
            'new_balance' => formatter_money($authWallet->amount),
            'transaction_id' => $trx,
            'voucher_number' => $voucher->code
        ]);


        notify($voucher->creator, $type = 'voucher_redeem_creator', [
            'amount' => formatter_money($voucher->amount),
            'currency' => $voucher->currency->code,
            'voucher_number' => $voucher->code,
            'by_username' => $authWallet->user->username,
            'by_fullname' => $authWallet->user->fullname,
            'by_email' => $authWallet->user->email,
        ]);

        $response['success'] = "Recharge Successful!";
        $response['result'] = true;
        return response($response);
    }

    public function voucherRedeemLog()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            $response = ['errors' => ["Voucher DeActive By Admin"]];
            return response($response);
        }
        $transactions = Voucher::with('currency')->where('use_id', Auth::id())->orderBy('updated_at', 'desc')->paginate(config('constants.table.default'));
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            return [
                'id' => $data->id,
                'date' => date('d M, Y', strtotime($data->updated_at)),
                'code' => $data->code,
                'amount' => formatter_money($data->amount) . " " . $data->currency->code,
                'useable_amount' => formatter_money($data->useable_amount) . " " . $data->currency->code,
                'charge' => formatter_money($data->use_charge) . " " . $data->currency->code,
            ];
        });
        return response($transactions, 200);
    }

    public function NewVoucherPreview(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'currency' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $basic = GeneralSetting::first();
        $voucher = json_decode($basic->voucher,false);

        $currency = Currency::where('id', $request->currency)->first();
        if(!$currency){
            $response = ['errors' => ['Invalid Currency!']];
            return response($response);
        }
        $minimumAmount = $voucher->new_voucher->minimum_amount;

        $percentCharge = $voucher->new_voucher->percent_charge;
        $fixedCharge = $voucher->new_voucher->fix_charge;
        $amount = formatter_money($request->amount);

        $charge = (($amount * $percentCharge)/100) + ($fixedCharge * $currency->rate);

        $vResult['amount'] = $amount;
        $vResult['percent_charge'] = formatter_money($percentCharge);
        $vResult['fixed_charge'] = formatter_money($fixedCharge * $currency->rate);
        $vResult['total_charge'] = formatter_money($charge);
        $vResult['payable'] = formatter_money($amount+$charge);
        $vResult['currency'] = $currency;
        $vResult['feedBack'] = true;
        return response($vResult,200);
    }

    public function createVoucher(Request $request)
    {
        $rules = [
            'amount' => ['required','numeric','regex:/^\d+(\.\d{1,2})?$/'],
            'currencyId' => ['required','numeric'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $reqCurrency = $request->currencyId;

        $auth = Auth::id();
        $wallet = Wallet::with('user')->where('user_id', $auth)->where('wallet_id', $reqCurrency)->first();
        if(!$wallet){
            $response = ['errors' => ["Invalid Wallet!"]];
            return response($response);
        }

        $currency = Currency::where('id', $reqCurrency)->firstOrFail();
        if(!$currency){
            $response = ['errors' => ["Invalid Currency!"]];
            return response($response);
        }

        $basic = GeneralSetting::first();

        $voucher = json_decode($basic->voucher,false);
        $percentCharge = $voucher->new_voucher->percent_charge;
        $fixedCharge = $voucher->new_voucher->fix_charge;
        $amount = formatter_money($request->amount);

        $charge = (($amount * $percentCharge)/100) + ($fixedCharge * $currency->rate);
        $totalAmount = formatter_money($amount + $charge);

        if ($wallet->amount >= $totalAmount) {
            $wallet->amount =  formatter_money($wallet->amount - $totalAmount);
            $wallet->save();

            $voucher =  Voucher::create([
                'user_id' => $auth,
                'amount' => $amount,
                'charge' => $charge,
                'code' => rand(10000000, 99999999) . rand(10000000, 99999999),
                'currency_id' => $currency->id,
                'status' => 0,
            ]);

            $trx = getTrx();

            Trx::create([
                'user_id' => $auth,
                'amount' => $amount,
                'main_amo' => formatter_money($wallet->amount),
                'charge' => formatter_money($charge),
                'currency_id' => $currency->id,
                'trx_type' => '-',
                'remark' => 'Voucher Create',
                'title' => $amount.' '. $currency->code .' Voucher Created ',
                'trx' => $trx
            ]);

            notify($wallet->user, $type = 'voucher_create', [
                'amount' => formatter_money($amount),
                'charge' => formatter_money($charge),
                'total' => formatter_money($amount + $charge),
                'currency' => $currency->code,
                'new_balance' => formatter_money($wallet->amount),
                'transaction_id' => $trx,
                'voucher_number' => $voucher->code
            ]);


            $response['success'] = "Voucher Code Generate successfully";
            $response['result'] = true;
            return response($response);

        } else {
            $response = ['errors' => ["Insufficient Balance!"]];
            return response($response);
        }

    }


}
