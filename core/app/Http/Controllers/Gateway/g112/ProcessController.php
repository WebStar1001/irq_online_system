<?php

namespace App\Http\Controllers\Gateway\g112;

use App\Deposit;
use App\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{

    /*
     * Instamojo Gateway
     */
    public static function process($deposit){
        $basic = GeneralSetting::first();
        $instaMojoAcc = json_decode($deposit->gateway_currency()->parameter);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$instaMojoAcc->api_key",
                "X-Auth-Token:$instaMojoAcc->auth_token"));

        $payload['purpose'] = 'Payment to '.$basic->sitename;
        $payload['amount'] = round($deposit->final_amo,2);

        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $payload['redirect_url'] = route('user.deposit');
        }else if($deposit->invoice_id != 0 ){
            $payload['redirect_url'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
        }else if($deposit->api_id != 0 ){
            $payload['redirect_url'] = route('express.payment',$deposit->express_payment->transaction);
        }


        if($deposit->user_id != null){
            $buyer_name = $deposit->user->username;
            $email = $deposit->user->email;
        }elseif ($deposit->invoice_id != 0){
            $buyer_name = $deposit->invoice_payment->name;
            $email = $deposit->invoice_payment->email;
        }elseif ($deposit->api_id != 0){
            $allData = json_decode($deposit->express_payment->all_data);
            $buyer_name = $allData->name;
            $email= $allData->email;
        }


        $payload['buyer_name'] = $buyer_name;
        $payload['email'] = $email;

        $payload['webhook'] = route('ipn.g112');
        $payload['send_email'] = true;
        $payload['allow_repeated_payments'] = false;

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response);
 
        if($res->success){
            $deposit->btc_wallet = $res->payment_request->id;
            $deposit->save();
            $send['redirect'] = true;
            $send['redirect_url'] = $res->payment_request->longurl;
        }else{
            $send['error'] = true;
            $send['message'] = "Invalid Request";
        }

        return json_encode($send);
    }



    public static function processApi($deposit){
        $basic = GeneralSetting::first();
        $instaMojoAcc = json_decode($deposit->gateway_currency()->parameter);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:$instaMojoAcc->api_key",
                "X-Auth-Token:$instaMojoAcc->auth_token"));

        $payload['purpose'] = 'Payment to '.$basic->sitename;
        $payload['amount'] = round($deposit->final_amo,2);

        $payload['redirect_url'] = route('api.deposit');

        $buyer_name = $deposit->user->username;
        $email = $deposit->user->email;

        $payload['buyer_name'] = $buyer_name;
        $payload['email'] = $email;

        $payload['webhook'] = route('ipn.g112');
        $payload['send_email'] = true;
        $payload['allow_repeated_payments'] = false;

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response);

        if($res->success){
            $deposit->btc_wallet = $res->payment_request->id;
            $deposit->save();

            $send['redirect'] = true;
            $send['redirect_url'] = $res->payment_request->longurl;
        }else{
            $send['error'] = true;
            $send['message'] = "Invalid Request";
        }

        return json_encode($send);
    }
    public function ipn(Request $request){

        $data = Deposit::where('btc_wallet', $_POST['payment_request_id'])->orderBy('id', 'DESC')->first();
        $instaMojoAcc = json_decode($data->gateway_currency()->parameter);

        $imData = $_POST;
        $macSent = $imData['mac'];
        unset($imData['mac']);
        ksort($imData, SORT_STRING | SORT_FLAG_CASE);
        $mac = hash_hmac("sha1", implode("|", $imData), $instaMojoAcc->salt);

        if($macSent == $mac && $imData['status'] == "Credit" && $data->status == '0'){
            PaymentController::userDataUpdate($data);
        }
    }

}
