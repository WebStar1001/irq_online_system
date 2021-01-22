<?php

namespace App\Http\Controllers\Gateway\g109;

use App\Deposit;
use App\ExpressPayment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Auth;

class ProcessController extends Controller
{
    /*
     * flutterwave Gateway
     */

    public static function process($deposit){
        $flutterAcc = json_decode($deposit->gateway_currency()->parameter);

        if($deposit->user_id != null){
            $customer_email = $deposit->user->email;
        }elseif ($deposit->invoice_id != 0){
            $customer_email = $deposit->invoice_payment->email;
        }elseif ($deposit->api_id != 0){
            $allData = json_decode($deposit->express_payment->all_data);
            $customer_email = $allData->email;
        }

        $send['API_publicKey'] = $flutterAcc->public_key;
        $send['customer_email'] = $customer_email;
        $send['amount'] = round($deposit->final_amo,2);
        $send['customer_phone'] = (Auth::user()) ? Auth::user()->mobile: '';
        $send['currency'] = $deposit->method_currency;
        $send['txref'] = $deposit->trx;
        $send['deposit'] = $deposit;

        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $send['view'] = 'payment.g109';
        }else{
            $send['view'] = 'apiPayment.g109';
        }
        return json_encode($send);
    }



    public static function processApi($deposit){
        $flutterAcc = json_decode($deposit->gateway_currency()->parameter);

        $customer_email = $deposit->user->email;
        $send['API_publicKey'] = $flutterAcc->public_key;
        $send['encryption_key'] = $flutterAcc->encryption_key;
        $send['customer_email'] = $customer_email;
        $send['amount'] = round($deposit->final_amo,2);
        $send['customer_phone'] = (Auth::user()) ? Auth::user()->mobile: '';
        $send['currency'] = $deposit->method_currency;
        $send['txref'] = $deposit->trx;
        return json_encode($send);
    }


    public function ipn($track, $type)
    {
        
        if ($type == 'error'){
            $notify[] = ['error', 'Transaction Failed, Ref: ' . $track];
        }else {

            if (isset($track)) {
                    
                $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
                $flutterAcc = json_decode($data->gateway_currency()->parameter);

                $query = array(
                    "SECKEY" =>  $flutterAcc->secret_key,
                    "txref" => $track
                );

                $data_string = json_encode($query);
                $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response);
                
                if ($response->data->status == "successful" && $response->data->chargecode == "00" && $data->final_amo == $response->data->amount && $data->method_currency == $response->data->currency && $data->status == '0') {
                    PaymentController::userDataUpdate($data);
                    $notify[] = ['success', 'Transaction was successful, Ref: ' . $track];

                    if($data->api_id != 0 ){
                        $express =  ExpressPayment::find($data->api_id);
                        return redirect(json_decode($express->all_data)->success_url);
                    }
                }else{
                   $notify[] = ['error', 'Unable to Process'];
                }
                }else{
                   $notify[] = ['error', 'Unable to Process'];
                }

                if($data->api_id == 0 && $data->invoice_id == 0 ){
                    return redirect()->route('user.deposit')->withNotify($notify);
                }else if($data->invoice_id != 0){
                    return redirect()->route('invoice.initiate.error')->with($notify);
                }else if($data->api_id != 0){
                    $express =  ExpressPayment::find($data->api_id);
                    return redirect(json_decode($express->all_data)->cancel_url);
                }

        }
    }



    public function ipnApi($track, $type)
    {

        if ($type == 'error'){
            return response(['errors' => 'Transaction Failed, Ref: '.$track]);
        }else {

            if (isset($track)) {

                $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
                if(!$data){
                    return response(['errors' => 'Invalid Request.']);
                }
                $flutterAcc = json_decode($data->gateway_currency()->parameter);

                $query = array(
                    "SECKEY" =>  $flutterAcc->secret_key,
                    "txref" => $track
                );

                $data_string = json_encode($query);
                $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response);

                if ($response->data->status == "successful" && $response->data->chargecode == "00" && $data->final_amo == $response->data->amount && $data->method_currency == $response->data->currency && $data->status == '0') {
                    PaymentController::userDataUpdate($data);
                    return response(['success' => 'Transaction was successful, Ref: ' . $track]);
                }else{
                    return response(['errors' => ['Unable to Process']]);
                }
            }else{
                return response(['errors' => ['Unable to Process']]);
            }

        }
    }






}
