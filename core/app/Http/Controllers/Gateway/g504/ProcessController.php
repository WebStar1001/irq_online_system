<?php

namespace App\Http\Controllers\Gateway\g504;

use App\Deposit;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * CoinPaymentHosted Gateway
     */

    public static function process($deposit){
        $basic =  GeneralSetting::first();
        $coinpayAcc = json_decode($deposit->gateway_currency()->parameter);
        
        $val['merchant'] = $coinpayAcc->merchant_id;
        $val['item_name'] = 'Payment to '.$basic->sitename;
        $val['currency'] = $deposit->method_currency;
        $val['currency_code'] = "$deposit->method_currency";
        $val['ipn_url'] =  route('ipn.g504');
        $val['custom'] = "$deposit->trx";
        $val['amount'] = $deposit->final_amo;

        $val['notify_url'] = route('ipn.g504');
        $val['cmd'] = '_pay_simple';
        $val['want_shipping'] = 0;

        if($deposit->api_id == 0 && $deposit->invoice_id == 0 ){
            $val['return'] = route('user.deposit');
            $val['cancel_return'] = route('user.deposit');
            $val['success_url'] = route('user.deposit');
            $val['cancel_url'] = route('user.deposit');
            $send['view'] = 'payment.redirect';
        }else if($deposit->invoice_id != 0 ){
            $val['return'] =  route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['cancel_return'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['success_url'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
            $val['cancel_url'] = route('getInvoice.payment',$deposit->invoice_payment->trx);
            $send['view'] = 'apiPayment.redirect';
        }else if($deposit->api_id != 0 ){
            $val['return'] =  route('express.payment',$deposit->express_payment->transaction);
            $val['cancel_return'] = route('express.payment',$deposit->express_payment->transaction);
            $allData = json_decode($deposit->express_payment->all_data);
            $val['success_url'] = $allData->cancel_url;
            $val['cancel_url'] = $allData->success_url;
            $send['view'] = 'apiPayment.redirect';
        }

        $send['method'] = 'post';
        $send['url'] = 'https://www.coinpayments.net/index.php';
        $send['val'] = $val;
        
        return json_encode($send);
    }



    public static function processApi($deposit){
        $basic =  GeneralSetting::first();
        $coinpayAcc = json_decode($deposit->gateway_currency()->parameter);

        $val['merchant'] = $coinpayAcc->merchant_id;
        $val['item_name'] = 'Payment to '.$basic->sitename;
        $val['currency'] = $deposit->method_currency;
        $val['currency_code'] = "$deposit->method_currency";
        $val['amount'] = $deposit->final_amo;
        $val['ipn_url'] =  route('ipn.g504');
        $val['custom'] = "$deposit->trx";

        $val['notify_url'] = route('ipn.g504');
        $val['cmd'] = '_pay_simple';
        $val['want_shipping'] = 0;

        $val['return'] = route('api.deposit');
        $val['cancel_return'] = route('api.deposit');
        $val['success_url'] = route('api.deposit');
        $val['cancel_url'] = route('api.deposit');

        $send['method'] = 'post';
        $send['url'] = 'https://www.coinpayments.net/index.php';
        $send['val'] = $val;

        $send['payment_url'] = 'https://www.coinpayments.net/index.php?merchant='.$val['merchant'].'&item_name='.$val['item_name'].'&currency='.$val['currency'].'&currency_code='.$val['currency_code'].'&amount='.$val['amount'].'&ipn_url='.$val['ipn_url'].'&custom='.$val['custom'].'&notify_url='.$val['notify_url'].'&cmd='.$val['cmd'].'&want_shipping='.$val['want_shipping'].'&return='.$val['return'].'&cancel_return='.$val['cancel_return'].'&success_url='.$val['success_url'].'&cancel_url='.$val['cancel_url'];

        return json_encode($send);
    }
    public function ipn(Request $request){

        $track = $request->custom;
        $status = $request->status;
        $amount1 = floatval($request->amount1);
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if ($status>=100 || $status==2){
            $coinPayAcc = json_decode($data->gateway_currency()->parameter);
            if ($data->method_currency == $request->currency1 && $data->final_amo <= $amount1  && $coinPayAcc->merchant_id == $request->merchant && $data->status == '0'){
                PaymentController::userDataUpdate($data);
            }
        }
    }




}
