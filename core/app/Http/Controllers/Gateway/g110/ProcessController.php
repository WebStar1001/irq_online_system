<?php

namespace App\Http\Controllers\Gateway\g110;

use App\Deposit;
use App\ExpressPayment;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use Session;
use Razorpay\Api\Api;
use Auth;
require_once('razorpay-php/Razorpay.php');

class ProcessController extends Controller
{
    /*
     * RazorPay Gateway
     */

    public static function process($deposit){
        $basic = GeneralSetting::first();
        $razorAcc = json_decode($deposit->gateway_currency()->parameter);

        //  API request and response for creating an order
        $api_key = $razorAcc->key_id;
        $api_secret = $razorAcc->key_secret;
        $api = new Api($api_key, $api_secret);

        $order = $api->order->create(array(
                'receipt' => $deposit->trx,
                'amount' => $deposit->final_amo*100,
                'currency' => $deposit->method_currency,
                'payment_capture' => '0'
            )
        );


        if($deposit->user_id != null){
            $name = $deposit->user->username;

            $prefillName = $deposit->user->username;
            $prefillEmail = $deposit->user->email;
            $prefillContact = $deposit->user->mobile;
        }elseif ($deposit->invoice_id != 0){
            $name = $deposit->invoice_payment->name;
            $prefillName = $deposit->invoice_payment->name;
            $prefillEmail = $deposit->invoice_payment->email;
            $prefillContact = '';
        }elseif ($deposit->api_id != 0){
            $allData = json_decode($deposit->express_payment->all_data);
            $name = $allData->name;
            $prefillName =  $allData->name;
            $prefillEmail =  $allData->email;
            $prefillContact = '';
        }

        $val['key'] = $razorAcc->key_id;
        $val['amount'] = $deposit->final_amo*100;
        $val['currency'] = $deposit->method_currency;
        $val['order_id'] = $order['id'];
        $val['buttontext'] = "Pay with Razorpay";
        $val['name'] = $name;
        $val['description'] = "Payment By Razorpay";
        $val['image'] = asset(config('constants.logoIcon.path') .'/logo.png');
        $val['prefill.name'] = $prefillName;
        $val['prefill.email'] = $prefillEmail;
        $val['prefill.contact'] = $prefillContact;
        $val['theme.color'] = "#".$basic->bclr;
        $send['val'] = $val;

        $send['method'] = 'POST';
        $send['url'] = route('ipn.g110');
        $send['custom'] = $deposit->trx;
        $send['checkout_js'] = "https://checkout.razorpay.com/v1/checkout.js";
        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $send['view'] = 'payment.g110';
        }else{
            $send['view'] = 'apiPayment.g110';
        }
        return json_encode($send);
    }


    public static function processApi($deposit){
        $basic = GeneralSetting::first();
        $razorAcc = json_decode($deposit->gateway_currency()->parameter);

        //  API request and response for creating an order
        $api_key = $razorAcc->key_id;
        $api_secret = $razorAcc->key_secret;
        $api = new Api($api_key, $api_secret);

        $order = $api->order->create(array(
                'receipt' => $deposit->trx,
                'amount' => $deposit->final_amo*100,
                'currency' => $deposit->method_currency,
                'payment_capture' => '0'
            )
        );

        $name = $deposit->user->username;
        $prefillName = $deposit->user->username;
        $prefillEmail = $deposit->user->email;
        $prefillContact = $deposit->user->mobile;


        $val['key'] = $razorAcc->key_id;
        $val['amount'] = $deposit->final_amo*100;
        $val['currency'] = $deposit->method_currency;
        $val['order_id'] = $order['id'];
        $val['buttontext'] = "Pay with Razorpay";
        $val['name'] = $name;
        $val['description'] = "Payment By Razorpay";
        $val['image'] = asset(config('constants.logoIcon.path') .'/logo.png');
        $val['prefill.name'] = $prefillName;
        $val['prefill.email'] = $prefillEmail;
        $val['prefill.contact'] = $prefillContact;
        $val['theme.color'] = "#".$basic->bclr;
        $send['val'] = $val;

        $send['method'] = 'POST';
        $send['url'] = route('ipn.api.g110',$deposit->trx);
        $send['custom'] = $deposit->trx;
        $send['checkout_js'] = "https://checkout.razorpay.com/v1/checkout.js";

        return json_encode($send);
    }




    public function ipn(Request $request)
    {
        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $razorAcc = json_decode($data->gateway_currency()->parameter);

        if(!$data){
            $notify[] = ['error', 'Invalid Request'];
        }

        $sig = hash_hmac('sha256', $request->razorpay_order_id. "|" .$request->razorpay_payment_id , $razorAcc->key_secret);

        if ($sig == $request->razorpay_signature && $data->status == '0') {
            PaymentController::userDataUpdate($data);
            $notify[] = ['success', 'Transaction was successful, Ref: ' . $track];
            if($data->api_id != 0 ){
                $express =  ExpressPayment::find($data->api_id);
                return redirect(json_decode($express->all_data)->success_url);
            }
        }else{
            $notify[] = ['error', "Invalid Request"];
        }

        if($data->api_id == 0 && $data->invoice_id == 0 ){
            return redirect()->route('user.deposit')->withNotify($notify);
        }else if($data->invoice_id != 0 ){
            return redirect()->route('invoice.initiate.error')->withNotify($notify);
        }else if($data->api_id != 0 ){
            $express =  ExpressPayment::find($data->api_id);
            return redirect(json_decode($express->all_data)->cancel_url);
        }
    }

    public function ipnApi($track, Request $request)
    {
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if(!$data){
            return response(['errors' => ["Invalid Request!"]]);
        }
        $razorAcc = json_decode($data->gateway_currency()->parameter);

        $sig = hash_hmac('sha256', $request->razorpay_order_id. "|" .$request->razorpay_payment_id , $razorAcc->key_secret);

        if ($sig == $request->razorpay_signature && $data->status == '0') {
            PaymentController::userDataUpdate($data);
            return response(['success' => 'Transaction was successful, Ref: ' . $track]);
        }else{
            return response(['errors' => ["Invalid Request!"]]);
        }
    }




}
