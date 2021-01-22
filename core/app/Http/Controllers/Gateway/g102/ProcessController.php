<?php

namespace App\Http\Controllers\Gateway\g102;

use App\Deposit;
use App\ExpressPayment;
use App\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Auth;

class ProcessController extends Controller
{

    /*
     * Perfect Money Gateway
     */
    public static function process($deposit){
        $basic =  GeneralSetting::first();
        $perfectAcc = json_decode($deposit->gateway_currency()->parameter);
        $val['PAYEE_ACCOUNT'] = trim($perfectAcc->wallet_id);
        $val['PAYEE_NAME'] = $basic->sitename;
        $val['PAYMENT_ID'] = "$deposit->trx";
        $val['PAYMENT_AMOUNT'] = round($deposit->final_amo,2);
        $val['PAYMENT_UNITS'] = "$deposit->method_currency";
        $val['STATUS_URL'] = route('ipn.g102');
        $val['SUGGESTED_MEMO'] = (Auth::user()) ? Auth::user()->username : '';
        $val['BAGGAGE_FIELDS'] = 'IDENT';


        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $val['PAYMENT_URL'] = route('user.deposit');
            $val['PAYMENT_URL_METHOD'] = 'POST';
            $val['NOPAYMENT_URL'] = route('user.deposit');
            $val['NOPAYMENT_URL_METHOD'] = 'POST';

            $send['view'] = 'payment.redirect';
        }else if($deposit->invoice_id != 0 ){
            $val['PAYMENT_URL'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['PAYMENT_URL_METHOD'] = 'GET';
            $val['NOPAYMENT_URL'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['NOPAYMENT_URL_METHOD'] = 'GET';

            $send['view'] = 'apiPayment.redirect';
        } else if($deposit->api_id != 0 ){

            $val['PAYMENT_URL'] = route('express.payment',$deposit->express_payment->transaction);
            $val['PAYMENT_URL_METHOD'] = 'GET';
            $val['NOPAYMENT_URL'] = route('express.payment',$deposit->express_payment->transaction);
            $val['NOPAYMENT_URL_METHOD'] = 'GET';
            $send['view'] = 'apiPayment.redirect';
        }

        $send['val'] = $val;
        $send['method'] = 'post';
        $send['url'] = 'https://perfectmoney.is/api/step1.asp';

        return json_encode($send);
    }


    /*
     * API Perfect Money Gateway
     */
    public static function processApi($deposit){
        $basic =  GeneralSetting::first();
        $perfectAcc = json_decode($deposit->gateway_currency()->parameter);
        $val['PAYEE_ACCOUNT'] = trim($perfectAcc->wallet_id);
        $val['PAYEE_NAME'] = $basic->sitename;
        $val['PAYMENT_ID'] = "$deposit->trx";
        $val['PAYMENT_AMOUNT'] = round($deposit->final_amo,2);
        $val['PAYMENT_UNITS'] = "$deposit->method_currency";
        $val['STATUS_URL'] = route('ipn.g102');
        $val['SUGGESTED_MEMO'] = (Auth::user()) ? Auth::user()->username : '';
        $val['BAGGAGE_FIELDS'] = 'IDENT';

        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $val['PAYMENT_URL'] = route('api.deposit');
            $val['PAYMENT_URL_METHOD'] = 'POST';
            $val['NOPAYMENT_URL'] = route('api.deposit');
            $val['NOPAYMENT_URL_METHOD'] = 'POST';
        }else if($deposit->api_id != 0 ){
            $express =  ExpressPayment::find($deposit->api_id);
            $val['PAYMENT_URL'] = json_decode($express->all_data)->success_url;
            $val['PAYMENT_URL_METHOD'] = 'GET';
            $val['NOPAYMENT_URL'] = json_decode($express->all_data)->cancel_url;
            $val['NOPAYMENT_URL_METHOD'] = 'GET';
            $send['view'] = 'apiPayment.redirect';
        }
        $send['val'] = $val;
        $send['method'] = 'post';
        $send['url'] = 'https://perfectmoney.is/api/step1.asp';

        $send['payment_url'] = 'https://perfectmoney.is/api/step1.asp?PAYEE_ACCOUNT='.trim($perfectAcc->wallet_id).'&PAYEE_NAME='.$basic->sitename.'&PAYMENT_ID='.$val['PAYMENT_ID'].'&PAYMENT_AMOUNT='.$val['PAYMENT_AMOUNT'].'&PAYMENT_UNITS='.$val['PAYMENT_UNITS'].'&STATUS_URL='.$val['STATUS_URL'].'&PAYMENT_URL='.$val['PAYMENT_URL'].'&PAYMENT_URL_METHOD=POST&NOPAYMENT_URL='.$val['NOPAYMENT_URL'].'&NOPAYMENT_URL_METHOD=POST&SUGGESTED_MEMO='.$val['SUGGESTED_MEMO'].'&BAGGAGE_FIELDS='.$val['BAGGAGE_FIELDS'];
        return json_encode($send);
    }


    public function ipn(){

            $data = Deposit::where('trx', $_POST['PAYMENT_ID'])->orderBy('id', 'DESC')->first();
            $pmAcc = json_decode($data->gateway_currency()->parameter);
            $passphrase = strtoupper(md5($pmAcc->passphrase));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        $string =
            $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
            $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
            $_POST['PAYMENT_BATCH_NUM'] . ':' .
            $_POST['PAYER_ACCOUNT'] . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));
        $hash2 = $_POST['V2_HASH'];
        
        if ($hash == $hash2) {

            $amo = $_POST['PAYMENT_AMOUNT'];
            $unit = $_POST['PAYMENT_UNITS'];
            $track = $_POST['PAYMENT_ID'];
            if ($_POST['PAYEE_ACCOUNT'] == $pmAcc->wallet_id && $unit == $data->method_currency && $amo == $data->final_amo && $data->status == '0') {
                //Update User Data
                PaymentController::userDataUpdate($data);
            }
        }
    }



}
