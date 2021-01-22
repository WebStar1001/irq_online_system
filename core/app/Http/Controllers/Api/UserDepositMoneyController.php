<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\Deposit;
use App\GatewayCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use File;
use Image;
use Validator;

class UserDepositMoneyController extends Controller
{
    public function depositLog()
    {
        $data['page_title'] = "Deposit Log";

        $transactions = Deposit::with('currency', 'gateway')->where('user_id', Auth::id())->where('status', '!=', 0)->latest()->paginate(15);
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            $bar = ($data->status == 1) ? "Complete" : (($data->status == 2)  ? "Pending" : (($data->status == -2)  ? "Rejected" : " "));
            return [
                'id' => $data->id,
                'trx' => $data->trx,
                'gateway' => $data->gateway->name,
                'amount' => formatter_money($data->amount) . " " . $data->currency->code,
                'payment_status' => [
                    'status' => $data->status,
                    'level' => $bar,
                ],
                'date' => date('d M, Y h:i A', strtotime($data->created_at))
            ];
        });


        return response($transactions,200);
    }

    public function deposit()
    {
        $response['gatewayCurrency'] = GatewayCurrency::with('method')->orderBy('method_code')->get()->map(function($item){
            return [
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'currency'=>$item->currency,
                    'symbol'=>$item->symbol,
                    'method_code'=>$item->method_code,
                    'min_amount'=>$item->min_amount,
                    'max_amount'=>$item->max_amount,
                    'percent_charge'=>$item->percent_charge,
                    'fixed_charge'=>$item->fixed_charge,
                    'rate'=>$item->rate,
                    'image'=>$item->image,
                    'gateway_parameter'=>json_decode($item->gateway_parameter),
                    'image_full_path'=>get_image(config('constants.deposit.gateway.path').'/'. $item->image),
                    'method' => [
                        'id' =>$item->method->id,
                        'code' =>$item->method->code,
                        'name' =>$item->method->name,
                        'alias' =>$item->method->alias,
                        'image' =>get_image(config('constants.deposit.gateway.path').'/'. $item->method->image),
                        'status' =>$item->method->status,
                        'supported_currencies' =>json_decode($item->method->supported_currencies),
                        'crypto' =>$item->method->crypto,
                        'description' =>$item->method->description
                        ]
                ];
        });
        $response['currency'] = Currency::whereStatus(1)->get();
        return response($response,200);
    }

    public function depositInsert(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'method_code' => 'required',
            'currency' => 'required',
            'currency_id' => 'required', // from currency
        ];
        $msg = [
            'currency_id.required' => 'Select a wallet to deposit'
        ];
        $validator = Validator::make($request->all(), $rules, $msg);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $gate = GatewayCurrency::where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            return response(['errors' => 'Invalid Gateway.']);
        }

        $walletCurrency = Currency::where('id', $request->currency_id)->where('status',1)->first();

        if (!$walletCurrency) {
            return response(['errors' => 'Invalid Currency.']);
        }


        if ($gate->min_amount <= $request->amount && $gate->max_amount >= $request->amount) {
            $charge = formatter_money($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
            $final_amo = formatter_money($request->amount + $charge);

            $destinationWalletAmount = $request->amount * $gate->rate * $walletCurrency->rate;

            $depo['currency_id'] = $walletCurrency->id;
            $depo['wallet_amount'] = formatter_money($destinationWalletAmount);

            $depo['user_id'] = Auth::id();
            $depo['method_code'] = $gate->method_code;
            $depo['method_currency'] = strtoupper($gate->currency);
            $depo['amount'] = $request->amount;
            $depo['charge'] = $charge;
            $depo['gate_rate'] = $gate->rate;
            $depo['cur_rate'] = $walletCurrency->rate;
            $depo['final_amo'] = formatter_money($final_amo);

            $depo['btc_amo'] = 0;
            $depo['btc_wallet'] = "";
            $depo['trx'] = getTrx();
            $depo['try'] = 0;
            $depo['status'] = 0;
            Deposit::create($depo);


            $data = Deposit::with('currency')->where('status', 0)->where('trx', $depo['trx'])->first();

            $response['trx'] = $data['trx'];
            $response['method_image']=  get_image(config('constants.deposit.gateway.path') .'/'. $data->gateway->image);
            $response['amount']=  formatter_money($data->amount) . ' '.$data->method_currency;
            $response['charge']=  formatter_money($data->charge) . ' '.$data->baseCurrency();
            $response['payable']=  $data->final_amo . ' '.$data->baseCurrency();
            $response['conversion_rate']= '1 '. $data->method_currency . ' = '.formatter_money($data->cur_rate * $data->gate_rate) . ' '.$data->currency->code;
            $response['you_will_get']= formatter_money($data->wallet_amount). ' '.$data->currency->code;

            if($data->gateway->crypto==1){
                $response['crypto_message'] = 'Conversion with ' .$data->method_currency .' and final value will Show on next step';
            }else{
                $response['crypto_message'] = null;
            }

            if($data->method_code > 999){
                $response['method_type'] = 'manual';
                $response['payment_url']= route('manualDeposit.form',$data['trx']);
            }else{
                $response['method_type'] = 'automatic';
                $response['payment_url']= route('deposit-api.confirm',$data['trx']);
            }

            return response($response,200);
        } else {
            return response(['errors' => 'Please Follow Deposit Limit.']);
        }
    }



    /*
     * Manual Payment Request
     */

    public function manualDepositForm($track)
    {
        $data = Deposit::with('gateway','currency')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return response(['errors' => 'Invalid Request.']);
        }
        if ($data->status != 0) {
            return response(['errors' => 'Invalid Request.']);
        }
        if($data->method_code > 999){
            $method = $data->gateway_currency();

            $response['trx'] =  $data->trx;
            $response['request_amount'] = formatter_money($data['amount']) . ' '.$data['method_currency'];
            $response['payable_amount'] = $data['final_amo']. ' '.$data['method_currency'];
            $extra = $data->gateway->extra;
            $response['payment_description'] = $data->gateway->description ;
            $response['delay'] = $extra->delay;


            $form = [];

            $form_file_field = [
                'level' => $extra->verify_image,
                'field_name' => 'verify_image',
                'type' => 'file',
            ];
            array_push($form,$form_file_field);

            foreach(json_decode($method->parameter) as $input){
                $form_field = [
                    'level' => $input,
                    'field_name' => 'ud['.str_slug($input).']',
                    'type' => 'input',
                ];
                array_push($form,$form_field);
            }

            $response['form'] = $form;
            $response['action'] = route('manualDeposit.update');
            $response['method'] = 'POST';

            return response($response);
        }
    }



    public function manualDepositUpdate(Request $request)
    {
        $rules = [
            'trx' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }
        $track = $request->trx;
        $data = Deposit::with('gateway','currency')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return response(['errors' => 'Invalid Request.']);
        }
        if ($data->status != 0) {
            return response(['errors' => 'Invalid Request.']);
        }


        $params = json_decode($data->gateway_currency()->parameter);
        if (!empty($params)) {
            foreach ($params as $param) {
                $validation_rule['ud.' . str_slug($param)] = 'required';
                $validation_msg['ud.*.required'] =  $param . ' is required';
            }
            $validator = Validator::make($request->all(), $validation_rule, $validation_msg);
            if ($validator->fails()) {
                return response(['errors' => $validator->errors()->all(), 422]);
            }
        }


        if ($request->hasFile('verify_image')) {
            try {
                $filename = upload_image($request->verify_image, config('constants.deposit.verify.path'));
                $data['verify_image'] = $filename;
            } catch (\Exception $exp) {
                return response(['errors' => 'Could not upload your verification image']);
            }
        }
        $data->detail =$request->ud;
        $data->status = 2; // pending
        $data->update();

        notify($data->user, $type = 'DEPOSIT_PENDING', [
            'trx' => $data->trx,
            'amount' => formatter_money($data->wallet_amount) . ' '.$data->method_currency,
            'method' => $data->gateway_currency()->name,
            'charge' => formatter_money($data->charge) . ' '.$data->method_currency,
        ]);
        $response = ['success' => "You have deposit request has been taken."];
        $response['result'] = true;
        return response($response);

    }







}
