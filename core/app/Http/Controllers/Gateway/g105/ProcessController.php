<?php

namespace App\Http\Controllers\Gateway\g105;

use App\Deposit;
use App\ExpressPayment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    /*
     * PayTM Gateway
     */

    public static function process($deposit)
    {
        $PayTmAcc = json_decode($deposit->gateway_currency()->parameter);
        $val['MID'] = trim($PayTmAcc->MID);
        $val['WEBSITE'] = trim($PayTmAcc->WEBSITE);
        $val['CHANNEL_ID'] = trim($PayTmAcc->CHANNEL_ID);
        $val['INDUSTRY_TYPE_ID'] = trim($PayTmAcc->INDUSTRY_TYPE_ID);

        $val['ORDER_ID'] = $deposit->trx;
        $val['TXN_AMOUNT'] = round($deposit->final_amo, 2);
        $val['CUST_ID'] = $deposit->user_id;
        $val['CALLBACK_URL'] = route('ipn.g105');
        $val['CHECKSUMHASH'] = (new PayTM())->getChecksumFromArray($val, $PayTmAcc->merchant_key);

        $send['val'] = $val;

        if ($deposit->api_id == 0 && $deposit->invoice_id == 0) {
            $send['view'] = 'payment.redirect';
        } else {
            $send['view'] = 'apiPayment.redirect';
        }

        $send['method'] = 'post';
        $send['url'] = $PayTmAcc->transaction_url . "?orderid=" . $deposit->trx;

        return json_encode($send);

    }


    public function ipn()
    {
        $data = Deposit::where('trx', $_POST['ORDERID'])->orderBy('id', 'DESC')->first();
        $PayTmAcc = json_decode($data->gateway_currency()->parameter);
        $ptm = new PayTM();

        if ($ptm->verifychecksum_e($_POST, $PayTmAcc->merchant_key, $_POST['CHECKSUMHASH']) === "TRUE") {

            if ($_POST['RESPCODE'] == "01") {
                $requestParamList = array("MID" => $PayTmAcc->MID, "ORDERID" => $_POST['ORDERID']);
                $StatusCheckSum = $ptm->getChecksumFromArray($requestParamList, $PayTmAcc->merchant_key);
                $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                $responseParamList = $ptm->callNewAPI($PayTmAcc->transaction_status_url, $requestParamList);
                if ($responseParamList['STATUS'] == 'TXN_SUCCESS' && $responseParamList['TXNAMOUNT'] == $_POST['TXNAMOUNT']) {
                    PaymentController::userDataUpdate($data);
                    $notify[] = ['success', 'Transaction is successful'];

                    if ($data->api_id != 0) {
                        $express = ExpressPayment::find($data->api_id);
                        return redirect(json_decode($express->all_data)->success_url);
                    }
                } else {
                    $notify[] = ['error', 'It seems some issue in server to server communication. Kindly connect with administrator'];
                }
            } else {
                $notify[] = ['error', $_POST['RESPMSG']];
            }
        } else {
            $notify[] = ['error', 'Security error!'];
        }

        if ($data->api_id == 0 && $data->invoice_id == 0) {
            return redirect()->route('user.deposit')->withNotify($notify);
        } else if ($data->invoice_id != 0) {
            return redirect()->route('invoice.initiate.error')->with($notify);
        } else if ($data->api_id != 0) {
            $express = ExpressPayment::find($data->api_id);
            return redirect(json_decode($express->all_data)->cancel_url);
        }
    }


    /*
     * API PayTM Gateway
     */
    public static function processApi($deposit)
    {
        $PayTmAcc = json_decode($deposit->gateway_currency()->parameter);

        $val['MID'] = trim($PayTmAcc->MID);
        $val['WEBSITE'] = trim($PayTmAcc->WEBSITE);
        $val['CHANNEL_ID'] = trim($PayTmAcc->CHANNEL_ID);
        $val['INDUSTRY_TYPE_ID'] = trim($PayTmAcc->INDUSTRY_TYPE_ID);

        $val['ORDER_ID'] = $deposit->trx;
        $val['TXN_AMOUNT'] = round($deposit->final_amo, 2);
        $val['CUST_ID'] = $deposit->user_id;
        $val['CALLBACK_URL'] = route('ipn.g105');
        $val['CHECKSUMHASH'] = (new PayTM())->getChecksumFromArray($val, $PayTmAcc->merchant_key);

        $send['val'] = $val;

        if ($deposit->api_id == 0 && $deposit->invoice_id == 0) {
            $send['view'] = 'payment.redirect';
        }
        $send['method'] = 'post';
        $send['url'] = $PayTmAcc->transaction_url . "?orderid=" . $deposit->trx;


        $post_params["CHECKSUMHASH"] = (new PayTM())->getChecksumFromArray($val, $PayTmAcc->merchant_key);
        $va = '';
        foreach($post_params as $k=>$v){
            $va .= '&'.$k.'='. $v;
        }
        $send['payment_url'] = $PayTmAcc->transaction_url ."?orderid=" . $deposit->trx.$va;
        return json_encode($send);

    }

    public function ipnApi()
    {

        $data = Deposit::where('trx', $_POST['ORDERID'])->orderBy('id', 'DESC')->first();
        $PayTmAcc = json_decode($data->gateway_currency()->parameter);
        $ptm = new PayTM();

        if ($ptm->verifychecksum_e($_POST, $PayTmAcc->merchant_key, $_POST['CHECKSUMHASH']) === "TRUE") {

            if ($_POST['RESPCODE'] == "01") {
                $requestParamList = array("MID" => $PayTmAcc->MID, "ORDERID" => $_POST['ORDERID']);
                $StatusCheckSum = $ptm->getChecksumFromArray($requestParamList, $PayTmAcc->merchant_key);
                $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                $responseParamList = $ptm->callNewAPI($PayTmAcc->transaction_status_url, $requestParamList);
                if ($responseParamList['STATUS'] == 'TXN_SUCCESS' && $responseParamList['TXNAMOUNT'] == $_POST['TXNAMOUNT']) {
                    PaymentController::userDataUpdate($data);

                    $response['success'] = 'Transaction is successful';
                } else {
                    $response['errors'] = ["It seems some issue in server to server communication. Kindly connect with administrator"];
                }
            } else {
                $response['errors'] = [$_POST['RESPMSG']];
            }
        } else {
            $response = ['errors' => ["Security error!"]];
        }


        if ($data->api_id == 0 && $data->invoice_id == 0) {
            $response['url'] = route('api.deposit');
            return response($response);
        }
    }


}
