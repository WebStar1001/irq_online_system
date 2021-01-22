<?php

namespace App\Http\Controllers\Gateway\g104;

use App\Deposit;
use App\ExpressPayment;
use App\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Session;
class ProcessController extends Controller
{

    /*
     * Skrill Gateway
     */
    public static function process($deposit){
         $basic =  GeneralSetting::first();
        $skrillAcc = json_decode($deposit->gateway_currency()->parameter);

        $val['pay_to_email'] = trim($skrillAcc->pay_to_email);
        $val['transaction_id'] = "$deposit->trx";

        $val['return_url_text'] = "Return $basic->sitename";
        $val['status_url'] = route('ipn.g104');
        $val['language'] = 'EN';
        $val['amount'] = round($deposit->final_amo,2);
        $val['currency'] = "$deposit->method_currency";
        $val['detail1_description'] = "$basic->sitename";
        $val['detail1_text'] = "Pay To $basic->sitename";
        $val['logo_url'] = asset('assets/images/logoIcon/logo.png');

        $send['val'] = $val;


        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $val['return_url'] = route('user.deposit');
            $val['cancel_url'] = route('user.deposit');
            $send['view'] = 'payment.redirect';
        }else if($deposit->invoice_id != 0 ){
            $val['return_url'] =  route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['cancel_url'] =  route('getInvoice.payment',$deposit->invoice_payment->trx);
            $send['view'] = 'apiPayment.redirect';
        }
        else if($deposit->api_id != 0 ){
            $express =  ExpressPayment::find($deposit->api_id);
            $val['return_url'] =  json_decode($express->all_data)->success_url;
            $val['cancel_url'] =  json_decode($express->all_data)->cancel_url;
            $send['view'] = 'apiPayment.redirect';
        }

        $send['method'] = 'post';
        $send['url'] = 'https://www.moneybookers.com/app/payment.pl';
        return json_encode($send);
    }



    /*
     * Skrill Gateway
     */
    public static function processApi($deposit){
        $basic =  GeneralSetting::first();
        $skrillAcc = json_decode($deposit->gateway_currency()->parameter);

        $val['pay_to_email'] = trim($skrillAcc->pay_to_email);
        $val['transaction_id'] = "$deposit->trx";

        $val['return_url_text'] = "Return $basic->sitename";
        $val['status_url'] = route('ipn.g104');
        $val['language'] = 'EN';
        $val['amount'] = round($deposit->final_amo,2);
        $val['currency'] = "$deposit->method_currency";
        $val['detail1_description'] = "$basic->sitename";
        $val['detail1_text'] = "Pay To $basic->sitename";
        $val['logo_url'] = asset('assets/images/logoIcon/logo.png');

        $send['val'] = $val;
        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $val['return_url'] = route('api.deposit');
            $val['cancel_url'] = route('api.deposit');
        }
        $send['method'] = 'post';
        $send['url'] = 'https://www.moneybookers.com/app/payment.pl';

        $send['payment_url'] = 'https://www.moneybookers.com/app/payment.pl?pay_to_email='.$val['pay_to_email'].'&transaction_id='.$val['transaction_id'].'&return_url='.$val['return_url'].'&return_url_text='.$val['return_url_text'].'&cancel_url='.$val['cancel_url'].'&status_url='.$val['status_url'].'&language=EN&amount='.$val['amount'].'&currency='.$val['currency'].'&detail1_description='.$val['detail1_description'].'&detail1_text='.$val['detail1_text'].'&logo_url='. $val['logo_url'].'';
        return json_encode($send);
    }
    
    public function ipn(){
        $data = Deposit::where('trx', $_POST['transaction_id'])->orderBy('id', 'DESC')->first();
        $SkrillrAcc = json_decode($data->gateway_currency()->parameter);
        $concatFields = $_POST['merchant_id']
            . $_POST['transaction_id']
            . strtoupper(md5($SkrillrAcc->secret_key))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

        if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == $SkrillrAcc->pay_to_email && $data->status = '0') {
            PaymentController::userDataUpdate($data);
        }
    }






}
