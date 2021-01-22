<?php

namespace App\Http\Controllers\Gateway\g106;

use App\Deposit;
use App\ExpressPayment;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * Payeer Gateway
     */

    public static function process($deposit)
    {
        $PayeerAcc = json_decode($deposit->gateway_currency()->parameter);
        $basic = GeneralSetting::first();
        $arHash = [$PayeerAcc->merchant_id, $deposit->trx, $deposit->final_amo, $deposit->method_currency, base64_encode("Pay To $basic->sitename"), $PayeerAcc->secret_key];
        $val['m_shop'] = trim($PayeerAcc->merchant_id);
        $val['m_orderid'] = $deposit->trx;
        $val['m_amount'] = round($deposit->final_amo, 2);
        $val['m_curr'] = $deposit->method_currency;
        $val['m_desc'] = base64_encode("Pay To $basic->sitename");
        $val['m_sign'] = strtoupper(hash('sha256', implode(":", $arHash)));
        $send['val'] = $val;
        if ($deposit->api_id == 0 && $deposit->invoice_id == 0) {
            $send['view'] = 'payment.redirect';
        } else {
            $send['view'] = 'apiPayment.redirect';
        }

        $send['method'] = 'get';
        $send['url'] = 'https://payeer.com/merchant';

        return json_encode($send);
    }


    public static function processApi($deposit)
    {
        $PayeerAcc = json_decode($deposit->gateway_currency()->parameter);
        $basic = GeneralSetting::first();
        $arHash = [$PayeerAcc->merchant_id, $deposit->trx, $deposit->final_amo, $deposit->method_currency, base64_encode("Pay To $basic->sitename"), $PayeerAcc->secret_key];
        $val['m_shop'] = trim($PayeerAcc->merchant_id);
        $val['m_orderid'] = $deposit->trx;
        $val['m_amount'] = round($deposit->final_amo, 2);
        $val['m_curr'] = $deposit->method_currency;
        $val['m_desc'] = base64_encode("Pay To $basic->sitename");
        $val['m_sign'] = strtoupper(hash('sha256', implode(":", $arHash)));
        $send['val'] = $val;
        if ($deposit->api_id == 0 && $deposit->invoice_id == 0) {
            $send['view'] = 'payment.redirect';
        }
        $send['method'] = 'get';
        $send['url'] = 'https://payeer.com/merchant';

        $send['payment_url'] = 'https://payeer.com/merchant' .'?m_shop='.$val['m_shop'].'&m_orderid='.$val['m_orderid'].'&m_amount='.$val['m_amount'].'&m_curr='.$val['m_curr'].'&m_desc='.$val['m_desc'].'&m_sign='.$val['m_sign'].'';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {

        if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"])) {

            $data = Deposit::where('trx', $_POST['m_orderid'])->orderBy('id', 'DESC')->first();
            $PayeerAcc = json_decode($data->gateway_currency()->parameter);
            $sign_hash = strtoupper(hash('sha256', implode(":", array(
                $_POST['m_operation_id'],
                $_POST['m_operation_ps'],
                $_POST['m_operation_date'],
                $_POST['m_operation_pay_date'],
                $_POST['m_shop'],
                $_POST['m_orderid'],
                $_POST['m_amount'],
                $_POST['m_curr'],
                $_POST['m_desc'],
                $_POST['m_status'],
                $PayeerAcc->secret_key
            ))));

            if ($_POST["m_sign"] != $sign_hash) {
                $notify[] = ['error', 'The digital signature did not matched.'];
            } else {

                if ($_POST['m_amount'] != $data->final_amo && $_POST['m_curr'] != $data->method_currency && $_POST['m_status'] == 'success' && $data->status == '0') {
                    PaymentController::userDataUpdate($data);
                    $notify[] = ['success', 'Transaction is successful'];

                    if ($data->api_id != 0) {
                        $express = ExpressPayment::find($data->api_id);
                        return redirect(json_decode($express->all_data)->success_url);
                    }

                } else {
                    $notify[] = ['error', 'Payment Failed.'];
                }

            }
        } else {
            $notify[] = ['error', 'Payment Failed.'];
        }


        if ($data->api_id == 0 && $data->invoice_id == 0) {
            return redirect()->route('user.deposit')->withNotify($notify);
        } else if ($data->invoice_id != 0) {
            return redirect()->route('invoice.initiate.error')->withNotify($notify);
        } else if ($data->api_id != 0) {
            $express = ExpressPayment::find($data->api_id);
            return redirect(json_decode($express->all_data)->cancel_url);
        }
    }


    public function ipnApi(Request $request)
    {

        if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"])) {

            $data = Deposit::where('trx', $_POST['m_orderid'])->orderBy('id', 'DESC')->first();
            $PayeerAcc = json_decode($data->gateway_currency()->parameter);
            $sign_hash = strtoupper(hash('sha256', implode(":", array(
                $_POST['m_operation_id'],
                $_POST['m_operation_ps'],
                $_POST['m_operation_date'],
                $_POST['m_operation_pay_date'],
                $_POST['m_shop'],
                $_POST['m_orderid'],
                $_POST['m_amount'],
                $_POST['m_curr'],
                $_POST['m_desc'],
                $_POST['m_status'],
                $PayeerAcc->secret_key
            ))));

            if ($_POST["m_sign"] != $sign_hash) {
                $response['errors'] = ["The digital signature did not matched."];
            } else {

                if ($_POST['m_amount'] != $data->final_amo && $_POST['m_curr'] != $data->method_currency && $_POST['m_status'] == 'success' && $data->status == '0') {
                    PaymentController::userDataUpdate($data);
                    $response['success'] = "Transaction is successful";
                } else {
                    $response['errors'] = ["Payment Failed."];
                }
                return response($response);

            }
        } else {
            $response['errors'] = ["Payment Failed."];
        }


        if ($data->api_id == 0 && $data->invoice_id == 0) {
            return response($response);
        }
    }


}
