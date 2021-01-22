<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\MoneyTransfer;
use App\Trx;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Validator;

class UserMoneyTransferController extends Controller
{
    public function moneyTransfer()
    {
        $basic = GeneralSetting::first();
        if($basic->mt_status == 0){
            $response = ['errors' => ["Money Transfer DeActive By Admin"]];
            return response($response);
        }
        $data['currency'] = Currency::latest()->whereStatus(1)->get();
        $data['money_transfer'] = json_decode($basic->money_transfer,false);

        return response($data,200);
    }

    public function startTransfer(Request $request)
    {
        $rules = [
            'amount' => 'required|min:0|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'sum' => 'required|min:0|numeric',
            'currency' => 'required',
            'receiver' => 'required',
            'protection' => ['required', Rule::in(['true', 'false'])],
        ];
        if($request->protection == 'true'){
            $request['code_protect'] =  $request->code_protect;
            $rules['code_protect'] = ['min:4','max:4'];
        }

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' =>$validator->errors()->all()],422);
        }


        $currency = Currency::where('id', $request->currency)->first();
        if(!$currency){
            $response = ['errors' => ["Invalid Currency!"]];
            return response($response);
        }
        $basic = GeneralSetting::first();
        $auth = Auth::user();
        $user = User::where('status',1)->where('id','!=',Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower(trim($request->receiver)))
                    ->orWhere('email', strtolower(trim($request->receiver)))
                    ->orWhere('mobile',trim($request->receiver));
            })->first();

        $money_transfer = json_decode($basic->money_transfer);


        $authWallet = Wallet::where('user_id', $auth->id)->where('wallet_id', $currency->id)->first();

        if ($user) {
            $amount = formatter_money($request->amount);
            $charge = formatter_money(((($amount * $money_transfer->percent_charge) / 100)+ ($money_transfer->fix_charge * $currency->rate)));

            $totalAmountBase = $amount + $charge;
            if ((($money_transfer->minimum_transfer * $currency->rate) < $totalAmountBase) && (($money_transfer->maximum_transfer * $currency->rate) > $totalAmountBase)) {

                if ($authWallet->amount >= $totalAmountBase) {
                    $receiver = $user;
                    $data['sender_id'] = $auth->id;
                    $data['receiver_id'] = $receiver->id;
                    $data['currency_id'] = $request->currency;
                    $data['amount'] = $amount;
                    $data['charge'] = $charge;
                    $data['code_protect'] = $request->code_protect;
                    $data['protection'] = $request->protection;
                    $data['note'] = $request->note;
                    $data['status'] = 0;
                    $data['trx'] = getTrx();
                    $authWallet->amount -= $totalAmountBase;

                    $moneyId = MoneyTransfer::create($data)->id;

                    return response(['result'=>true,'url'=>route('api.previewTransfer', $moneyId)]);

                } else {
                    $response = ['errors' => ["In Sufficient Balance!"]];
                    return response($response);
                }
            } else {
                $response = ['errors' => ["Follow Money Transfer limit " .(formatter_money($money_transfer->minimum_transfer * $currency->rate)) .' - '. (formatter_money($money_transfer->maximum_transfer * $currency->rate)) . " $currency->code "]];
                return response($response);
            }
        }else{
            $response = ['errors' => ["Invalid User!"]];
            return response($response);
        }
    }

    public function previewTransfer($moneyId)
    {
        $basic = GeneralSetting::first();
        if($basic->mt_status == 0){
            $response = ['errors' => ["Money Transfer DeActive By Admin"]];
            return response($response);
        }

        $transfer = MoneyTransfer::where('id',$moneyId)->where('sender_id', auth()->id())->where('status', 0)->first();

        if (!$transfer){
            $response = ['errors' => ["Invalid Data"]];
            return response($response);
        }

        $authWallet = Wallet::where('user_id', $transfer->sender_id)->where('wallet_id', $transfer->currency_id)->first();

        $result['Amount'] = formatter_money($transfer->amount).' '.$transfer->currency->code;
        $result['Charge'] = formatter_money($transfer->charge).' '.$transfer->currency->code;
        $result['Payable'] = formatter_money(($transfer->amount+$transfer->charge)) . ' '.$transfer->currency->code;
        $result['remaining_balance'] = [
            'title' => 'Remaining '. $transfer->currency->code . ' Balance',
            'balance' => ($authWallet->amount - ($transfer->amount+$transfer->charge)) . ' '.$transfer->currency->code
        ];
        $result['payment_need']=  ($transfer->status ==0)? true:false;
        $result['id']=  $transfer->id;
        return response($result , 200);
    }

    public function confirmTransfer(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' =>$validator->errors()->all()],422);
        }

        $trans = MoneyTransfer::where('id',$request->id)->where('status',0)->first();
        if(!$trans){
            return response(['errors' =>'Invalid Request!'],422);
        }

        $authWallet = Wallet::where('user_id', $trans->sender_id)->where('wallet_id', $trans->currency_id)->first();
        if(!$authWallet){
            return response(['errors' =>'Invalid Wallet!'],422);
        }


        if($authWallet->amount < round(($trans->amount + $trans->charge), 2)){
            $response = ['errors' => ["Insufficient Balance!"]];
            return response($response);
        }

        if($trans->protection == 'false'){
            $authWallet->amount -= formatter_money(($trans->amount + $trans->charge));
            $authWallet->save();

            $receiverWallet = Wallet::where('user_id', $trans->receiver_id)->where('wallet_id', $trans->currency_id)->first();
            $receiverWallet->amount += formatter_money($trans->amount);
            $receiverWallet->save();

            $trans->status = 1;
            $trans->save();

            $trx = getTrx();

            Trx::create([
                'user_id' => $authWallet->user_id,
                'amount' => formatter_money($trans->amount),
                'main_amo' => formatter_money($authWallet->amount),
                'charge' => formatter_money($trans->charge),
                'currency_id' => $trans->currency_id,
                'trx_type' => '-',
                'remark' => 'Send Money',
                'title' => 'Send Money Transfer To  ' . $receiverWallet->user->username,
                'trx' => $trx
            ]);

            Trx::create([
                'user_id' => $receiverWallet->user_id,
                'amount' => formatter_money($trans->amount),
                'main_amo' => formatter_money($receiverWallet->amount),
                'charge' => 0,
                'currency_id' => $trans->currency_id,
                'trx_type' => '+',
                'remark' => 'Receive Money',
                'title' => 'Receive Money  From ' . $authWallet->user->username,
                'trx' => $trx
            ]);



            // Receiver
            notify($receiverWallet->user, $type = 'money_transfer_receiver', [
                'amount' => $trans->amount,
                'currency' => $receiverWallet->currency->code,
                'from_username' => $authWallet->user->username,
                'from_fullname' => $authWallet->user->fullname,
                'from_email' => $authWallet->user->email,
                'transaction_id' => $trx,
                'message' => $trans->note,
                'current_balance' => formatter_money($receiverWallet->amount),
            ]);

            // Sender
            notify($authWallet->user, $type = 'money_transfer_send', [
                'amount' => $trans->amount,
                'currency' => $authWallet->currency->code,
                'to_username' => $receiverWallet->user->username,
                'to_fullname' => $receiverWallet->user->fullname,
                'to_email' => $receiverWallet->user->email,
                'transaction_id' => $trx,
                'message' => $trans->note,
                'current_balance' => formatter_money($authWallet->amount),
            ]);

            $response = ['success' => "Amount Send  Successfully!"];
            $response['result'] = true;
            return response($response);
        }else{
            $authWallet->amount -= formatter_money(($trans->amount + $trans->charge));
            $authWallet->save();

            $receiverWallet = Wallet::where('user_id', $trans->receiver_id)->where('wallet_id', $trans->currency_id)->first();
            $trans->status = 2;
            $trans->save();

            $trx = getTrx();

            Trx::create([
                'user_id' => $authWallet->user_id,
                'amount' => formatter_money($trans->amount),
                'main_amo' => formatter_money($authWallet->amount),
                'charge' => formatter_money($trans->charge),
                'currency_id' => $trans->currency_id,
                'trx_type' => '-',
                'remark' => 'Deal To Send Money',
                'title' => 'Make a deal for Money Transfer To ' . $receiverWallet->user->username,
                'trx' => $trx
            ]);

            notify($receiverWallet->user, $type = 'deal_transfer', [
                'amount' => $trans->amount,
                'currency_code' => $receiverWallet->currency->code,
                'user_name' => $authWallet->user->username,
                'message' => $trans->note
            ]);

            $response = ['success' => "Make a deal Successfully!"];
            $response['result'] = true;
            return response($response);
        }
    }


    public function transferOutgoing()
    {
        $basic = GeneralSetting::first();
        if($basic->mt_status == 0){
            $response = ['errors' => ["Money Transfer DeActive By Admin"]];
            return response($response);
        }

        $moneyTransfer =   MoneyTransfer::with('receiver','currency')->where('sender_id',auth()->id())->where('status','!=',0)->latest()->paginate(15);


        $moneyTransfer = resourcePaginate($moneyTransfer, function ($data) use ($moneyTransfer) {
            if($data->status == 1){
                $level = 'Paid';
            }elseif ($data->status == 2){
                $level = 'Pending';
            }elseif ($data->status == -2){
                $level = 'Refund';
            }

            $code_protect = ($data->protection != 'true') ? null : ['level' => 'Code Protect','data' => $data->code_protect] ;

            return [
                'Trx' =>$data->trx,
                'Date' =>date('d M, Y', strtotime($data->created_at)),
                'Receiver' =>$data->receiver->username,
                'Amount' =>formatter_money($data->amount).' '.$data->currency->code,
                'Status' =>$data->status,
                'level' =>$level,
                'full_name' => [
                    'level' => 'Receiver Full Name',
                    'data' => $data->receiver->fullname,
                ],
                'receiver_email' => [
                    'level' => 'Receiver Email',
                    'data' => $data->receiver->email,
                ],
                'receiver_contact' => [
                    'level' => 'Receiver Contact Info',
                    'data' => $data->receiver->mobile,
                ],
                'send_amount' => [
                    'level' => 'Send Amount',
                    'data' => $data->amount .' '.$data->currency->code,
                ],
                'send_charge' => [
                    'level' => 'Charge',
                    'data' => $data->charge .' '.$data->currency->code,
                ],
                'protection' =>$data->protection,
                'code_protect' => $code_protect,
                'transfer_note' => [
                    'level' => 'Note',
                    'data' => $data->note,
                ]


            ];
        });

        return response($moneyTransfer,200);
    }


    public function transferIncoming()
    {
        $basic = GeneralSetting::first();
        if($basic->mt_status == 0){
            $response = ['errors' => ["Money Transfer DeActive By Admin"]];
            return response($response);
        }

        $response['moneyTransferProtected'] =   MoneyTransfer::with('sender','currency')->where('receiver_id',auth()->id())->whereIn('status',[2])->latest()->get()->map(function ($data){

            if($data->status == 1){
                $level = 'Paid';
            }elseif ($data->status == 2){
                $level = 'Pending';
            }elseif ($data->status == -2){
                $level = 'Cancel';
            }
            return [
                'id' => $data->id,
                'code' => $data->code_protect,
                'money' => formatter_money($data->amount) . ' '.$data->currency->code,
                'Status' =>$data->status,
                'level' =>$level,
                'full_name' => [
                    'level' => 'Sender Full Name',
                    'data' => $data->sender->fullname,
                ],
                'sender_email' => [
                    'level' => 'Sender Email',
                    'data' => $data->sender->email,
                ],
                'sender_contact' => [
                    'level' => 'Sender Contact Info',
                    'data' => $data->sender->mobile,
                ],
                'receive_amount' => [
                    'level' => 'Receive Amount',
                    'data' => $data->amount .' '.$data->currency->code,
                ],
                'protection' =>$data->protection,
                'transfer_note' => [
                    'level' => 'Note',
                    'data' => $data->note,
                ],
                'Date' =>date('d M, Y', strtotime($data->created_at)),
            ];
        });

        $moneyTransfer =   MoneyTransfer::with('sender','currency')->where('receiver_id',auth()->id())->whereIn('status',[-2,1])->latest()->paginate(20);

        $response['moneyTransfer'] = resourcePaginate($moneyTransfer, function ($data) use ($moneyTransfer) {
            if($data->status == 1){
                $level = 'Paid';
            }elseif ($data->status == 2){
                $level = 'Pending';
            }elseif ($data->status == -2){
                $level = 'Refund';
            }

            return [
                'id' => $data->id,
                'code' => $data->code_protect,
                'money' => formatter_money($data->amount) . ' '.$data->currency->code,
                'Status' =>$data->status,
                'level' =>$level,
                'full_name' => [
                    'level' => 'Sender Full Name',
                    'data' => $data->sender->fullname,
                ],
                'sender_email' => [
                    'level' => 'Sender Email',
                    'data' => $data->sender->email,
                ],
                'sender_contact' => [
                    'level' => 'Sender Contact Info',
                    'data' => $data->sender->mobile,
                ],
                'receive_amount' => [
                    'level' => 'Receive Amount',
                    'data' => $data->amount .' '.$data->currency->code,
                ],
                'transfer_note' => [
                    'level' => 'Note',
                    'data' => $data->note,
                ],
                'Date' =>date('d M, Y', strtotime($data->created_at)),
            ];
        });
        return response($response,200);
    }


    public function transferRelease(Request $request)
    {
        $rules = [
            'id' => 'required',
            'code' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' =>$validator->errors()->all()]);
        }

        $trans =  MoneyTransfer::where('id',$request->id)->where('receiver_id', auth()->id())->where('status',2)->first();
        if(!$trans){
            return response(['errors' =>'Invalid Request']);
        }

        if($trans->protection != 'true'){
            return response(['errors' =>'This Amount Not Protected']);
        }

        if($trans->code_protect != trim($request->code)){
            return response(['errors' =>'Invalid Code']);
        }



        $receiverWallet = Wallet::with('currency')->where('user_id', $trans->receiver_id)->where('wallet_id', $trans->currency_id)->first();
        $receiverWallet->amount += formatter_money($trans->amount);
        $receiverWallet->save();

        $trans->status = 1;
        $trans->update();

        Trx::create([
            'user_id' => $receiverWallet->user_id,
            'amount' => formatter_money($trans->amount),
            'main_amo' => formatter_money($receiverWallet->amount),
            'charge' => 0,
            'currency_id' => $trans->currency_id,
            'trx_type' => '+',
            'remark' => 'Receive Money',
            'title' => 'Receive Money From ' . $trans->sender->username,
            'trx' => $trans->trx
        ]);


        notify($receiverWallet->user, $type = 'money_transfer_receiver', [
            'amount' => $trans->amount,
            'currency' => $receiverWallet->currency->code,
            'from_username' => $trans->sender->username,
            'from_fullname' => $trans->sender->fullname,
            'from_email' => $trans->sender->email,
            'transaction_id' => $trans->trx,
            'message' => null,
            'current_balance' => formatter_money($receiverWallet->amount),
        ]);

        $response['success'] = "Amount Send Successfully!";
        $response['result'] = true;
        return response($response,200);

    }





}
