<?php
namespace App\Http\Controllers\Gateway\g108;

use App\Deposit;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Gateway\PaymentController;
use Validator;

class ProcessController extends Controller
{
    /*
     * Vogue Pay Gateway
     */

    public static function process($deposit){
        $vogueAcc = json_decode($deposit->gateway_currency()->parameter);
        $send['v_merchant_id'] = $vogueAcc->merchant_id;
        $send['notify_url'] = route('ipn.g108');
        $send['cur'] = $deposit->method_currency;
        $send['merchant_ref'] = $deposit->trx;
        $send['memo'] = 'Payment';
        $send['store_id'] = $deposit->user_id;
        $send['custom'] = $deposit->trx;
        $send['Buy'] = round($deposit->final_amo,2);
        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $send['view'] = 'payment.g108';
        }else{
            $send['view'] = 'apiPayment.g108';
        }

        $send['deposit'] = $deposit;
        return json_encode($send);
    }

    public static function processApi($deposit){
        $vogueAcc = json_decode($deposit->gateway_currency()->parameter);
        $send['v_merchant_id'] = $vogueAcc->merchant_id;
        $send['notify_url'] = route('ipn.api.g108');
        $send['cur'] = $deposit->method_currency;
        $send['merchant_ref'] = $deposit->trx;
        $send['memo'] = 'Payment';
        $send['store_id'] = $deposit->user_id;
        $send['custom'] = $deposit->trx;
        $send['Buy'] = round($deposit->final_amo,2);

        $send['form_message'] = [
            'pay_amount' => formatter_money($deposit->final_amo,2) . ' '.$deposit->method_currency,
            'to_get' => formatter_money($deposit->wallet_amount) . ' '.$deposit->currency->code
        ];

        return json_encode($send);
    }
    
    public function ipn(Request $request){
        $request->validate([
            'transaction_id' => 'required'
        ]);

        $trx = $request->transaction_id;
        $req_url = "https://voguepay.com/?v_transaction_id=$trx&type=json";
        $vougueData = curlContent($req_url);
        $vougueData = json_decode($vougueData);
        $track = $vougueData->merchant_ref;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $vogueAcc = json_decode($data->gateway_currency()->parameter);

        if ($vougueData->status == "Approved" && $vougueData->merchant_id == $vogueAcc->merchant_id && $vougueData->total== $data->final_amo && $vougueData->cur_iso==$data->method_currency &&  $data->status == '0') {
            //Update User Data
            PaymentController::userDataUpdate($data);
        }
    }

    public function ipnApi(Request $request){
        $rules = [
            'transaction_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $trx = $request->transaction_id;
        $req_url = "https://voguepay.com/?v_transaction_id=$trx&type=json";
        $vougueData = curlContent($req_url);
        $vougueData = json_decode($vougueData);
        $track = $vougueData->merchant_ref;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if(!$data){
            return response(['errors' => 'Invalid Request']);
        }

        $vogueAcc = json_decode($data->gateway_currency()->parameter);
        if ($vougueData->status == "Approved" && $vougueData->merchant_id == $vogueAcc->merchant_id && $vougueData->total== $data->final_amo && $vougueData->cur_iso==$data->method_currency &&  $data->status == '0') {
            //Update User Data
            PaymentController::userDataUpdate($data);
        }
    }





}
