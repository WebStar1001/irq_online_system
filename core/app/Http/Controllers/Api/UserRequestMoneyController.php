<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\RequestMoney;
use App\Trx;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Validator;

class UserRequestMoneyController extends Controller
{
    public function moneyTransfer()
    {
        $basic = GeneralSetting::first();
        if ($basic->mt_status == 0) {
            $response = ['errors' => ["Money Transfer DeActive By Admin"]];
            return response($response);
        }
        $data['currency'] = Currency::latest()->whereStatus(1)->get();
        $data['page_title'] = "Money Transfer";
        $data['money_transfer'] = json_decode($basic->money_transfer, false);

        return response($data, 200);
    }

    public function makeRequestMoney()
    {
        $basic = GeneralSetting::first();
        if ($basic->rqm_status == 0) {
            $response = ['errors' => ["Request Money DeActive By Admin"]];
            return response($response);
        }
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['request_money'] = json_decode($basic->request_money);

        return response($data, 200);
    }

    public function requestMoneyStore(Request $request)
    {
        $rules = [
            'title' => 'required',
            'receiver' => 'required',
            'amount' => 'required|numeric|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'currency' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $basic = GeneralSetting::first();
        $request_money = json_decode($basic->request_money);

        $user = User::where('status', 1)->where('id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower($request->receiver))
                    ->orWhere('email', strtolower($request->receiver))
                    ->orWhere('mobile', $request->receiver);
            })->first();

        if (!$user) {
            $response = ['errors' => ["This User Could not Found!"]];
            return response($response);
        }

        $currency = Currency::where('id', $request->currency)->first();
        if (!$currency) {
            $response = ['errors' => ["Invalid Currency!"]];
            return response($response);
        }

        $data['title'] = $request->title;
        $data['sender_id'] = Auth::id();
        $data['receiver_id'] = $user->id;
        $data['currency_id'] = $request->currency;
        $data['amount'] = formatter_money($request->amount);
        $data['charge'] = formatter_money((($request->amount * $request_money->percent_charge) / 100) + ($request_money->fix_charge * $currency->rate));
        $data['trx'] = getTrx();
        $data['info'] = $request->info;
        $data['status'] = 0;
        $requestMon = RequestMoney::create($data);

        notify($user, $type = 'request_money', [
            'request_amount' => formatter_money($request->amount),
            'request_currency' => $currency->code,
            'message' => $requestMon->title,
            'details' => $requestMon->info,
            'sender' => Auth::user()->email
        ]);
        $response['success'] = "Request Send Successfully";
        $response['result'] = true;
        return response($response);

    }


    public function requestMoneySendLog()
    {
        $basic = GeneralSetting::first();
        if($basic->rqm_status == 0){
            $response = ['errors' => ["Request Money DeActive By Admin"]];
            return response($response);
        }
        $transactions = RequestMoney::with('currency','receiver','user')->where('sender_id', Auth::id())->latest()->paginate(15);
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {

            if($data->status == 0){
                $level = "Pending";
            }elseif($data->status == 1){
                $level = "Paid";
            }elseif($data->status == -1){
                $level = "Rejected";
            }

            return [
                'id' =>$data->id,
                'trx' =>strtoupper($data->trx),
                'date' =>date('d M, Y',strtotime($data->created_at)),
                'username' =>$data->receiver->username,
                'level' =>$level,
                'status' =>$data->status,
                'datetime' =>date('d M, Y h:i A',strtotime($data->created_at)),
                'sender' =>$data->user->username,
                'receiver' =>[
                    'username' => $data->receiver->username,
                    'email' => $data->receiver->email,
                    'mobile' => $data->receiver->mobile,
                ],
                'amount' => formatter_money($data->amount)." ".$data->currency->code,
                'charge' => formatter_money($data->charge)." ".$data->currency->code,
                'total_amount' => formatter_money($data->amount-$data->charge)." ".$data->currency->code,
                'title' => $data->title,
                'details' => $data->info,
            ];
        });
        return response($transactions, 200);
    }

    public function requestMoney()
    {
        $basic = GeneralSetting::first();
        if($basic->rqm_status == 0){
            $response = ['errors' => ["Request Money DeActive By Admin"]];
            return response($response);
        }
        $transactions = RequestMoney::with('currency','user','receiver')->where('receiver_id', Auth::id())->latest()->paginate(15);

        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {

            if($data->status == 0){
                $level = "Pending";
            }elseif($data->status == 1){
                $level = "Paid";
            }elseif($data->status == -1){
                $level = "Unpaid";
            }

            $action =  ($data->status != 0) ? false : ['approve' => 1,'reject' => -1];
            return [
                'id' =>$data->id,
                'trx' =>strtoupper($data->trx),
                'date' =>date('d M, Y',strtotime($data->created_at)),
                'username' =>$data->user->username,
                'level' =>$level,
                'status' =>$data->status,
                'datetime' =>date('d M, Y h:i A',strtotime($data->created_at)),
                'receiver' =>$data->receiver->username,
                'sender' =>[
                    'username' => $data->user->username,
                    'email' => $data->user->email,
                    'mobile' => $data->user->mobile,
                ],
                'amount' => formatter_money($data->amount)." ".$data->currency->code,
                'charge' => formatter_money(0)." ".$data->currency->code,
                'total_amount' => formatter_money($data->amount)." ".$data->currency->code,
                'title' => $data->title,
                'details' => $data->info,
                'action' =>$action
            ];
        });
        return response($transactions, 200);
    }




    public function moneyReceivedAction(Request $request)
    {

        $rules = [
            'approve' => ['required',Rule::in(['1', '-1'])],
            'id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }



        if ($request->approve == 1) {
            $requestInvoice = RequestMoney::with('currency','receiver','user')->whereId($request->id)->where('receiver_id', Auth::id())->first();

            if(!$requestInvoice || $requestInvoice->status != 0){
                $response = ['errors' => ["Invalid Request!"]];
                return response($response);
            }

            $amountCharge = $requestInvoice->amount - $requestInvoice->charge;
            $authWallet = Wallet::with('currency','user')->where('user_id', Auth::id())->where('wallet_id', $requestInvoice->currency_id)->first();

            if(!$authWallet){
                $response = ['errors' => ["Invalid Wallet"]];
                return response($response);
            }

            if ($authWallet->amount >= $amountCharge) {
                $authWallet->amount = formatter_money($authWallet->amount - $requestInvoice->amount);
                $authWallet->update();

                $requestInvoice->status = 1;
                $requestInvoice->update();


                $senderWallet = Wallet::with('currency','user')->where('user_id', $requestInvoice->sender_id)->where('wallet_id', $requestInvoice->currency_id)->first();
                $senderWallet->amount +=  formatter_money($senderWallet->amount + ($requestInvoice->amount-$requestInvoice->charge));
                $senderWallet->update();


                $trx = getTrx();

                Trx::create([
                    'user_id' => $authWallet->user_id,
                    'amount' => $requestInvoice->amount,
                    'main_amo' => formatter_money($authWallet->amount),
                    'charge' => 0,
                    'currency_id' => $authWallet->wallet_id,
                    'trx_type' => '-',
                    'remark' => 'Request Amount Accepted',
                    'title' => $requestInvoice->amount .' '. $requestInvoice->currency->code .' Request Amount Paid to ' . $requestInvoice->user->username,
                    'trx' => $trx
                ]);

                Trx::create([
                    'user_id' => $senderWallet->user_id,
                    'amount' => formatter_money($requestInvoice->amount - $requestInvoice->charge),
                    'main_amo' => formatter_money($senderWallet->amount),
                    'charge' => formatter_money($requestInvoice->charge),
                    'currency_id' => $senderWallet->wallet_id,
                    'trx_type' => '+',
                    'remark' => 'Request Amount Accepted',
                    'title' => $requestInvoice->amount .' '. $requestInvoice->currency->code .' Request Amount Paid By ' . $requestInvoice->receiver->username,
                    'trx' => $trx
                ]);



                notify($authWallet->user, $type = 'request_money_send', [
                    'amount' => formatter_money($requestInvoice->amount),
                    'main_balance' => formatter_money($authWallet->amount),
                    'currency' => $requestInvoice->currency->code,
                    'by_username' => $senderWallet->user->username,
                    'by_fullname' => $senderWallet->user->fullname,
                    'by_email' => $senderWallet->user->email,
                    'message' => $requestInvoice->title,
                    'details' =>$requestInvoice->info
                ]);


                notify($senderWallet->user, $type = 'request_money_receive', [
                    'amount' => formatter_money($requestInvoice->amount - $requestInvoice->charge),
                    'main_balance' => formatter_money($senderWallet->amount),
                    'currency' => $requestInvoice->currency->code,
                    'to_username' => $senderWallet->user->username,
                    'to_fullname' => $senderWallet->user->fullname,
                    'to_email' => $senderWallet->user->email,
                    'message' => $requestInvoice->title,
                    'details' =>$requestInvoice->info
                ]);


                $response['success'] = "Request Money Approved Successfully";
                $response['result'] = true;
                return response($response);
            } else {

                $response = ['errors' => ["Insufficient Balance"]];
                return response($response);
            }
        } elseif ($request->approve == -1) {
            $invoice = RequestMoney::where('id',$request->id)->where('receiver_id', Auth::id())->first();
            if(!$invoice || $invoice->status != 0){
                $response = ['errors' => ["Invalid Request!"]];
                return response($response);
            }
            $invoice->status = -1;
            $invoice->save();

            $response['success'] = "Rejected Successfully";
            $response['result'] = true;
            return response($response);
        }
        abort(404);
    }


}
