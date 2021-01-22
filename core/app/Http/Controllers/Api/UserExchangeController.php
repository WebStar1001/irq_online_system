<?php

namespace App\Http\Controllers\API;

use App\ExchangeMoney;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Currency;
use App\GeneralSetting;
use App\Trx;
use App\User;
use App\Wallet;
use Auth;
use Illuminate\Validation\Rule;
use Validator;

class UserExchangeController extends Controller
{
    public function exchangeCalculation(Request $request)
    {

        $rules = [
            'amount' => 'required|numeric|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'from_currency_id' => 'required',
            'to_currency_id' => 'required'
        ];

        $msg = [
            'amount.required' => 'Please enter amount',
            'from_currency_id.required' => 'From Currency Must Be Selected',
            'to_currency_id.required' => 'Receive Currency Must Be Selected',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $basic = GeneralSetting::first();
        $money_exchange = json_decode($basic->money_exchange);
        $amount = formatter_money($request->amount);

        $fromCurrency = Currency::where('id', $request->from_currency_id)->first();
        $toCurrency = Currency::where('id', $request->to_currency_id)->first();


        if(!$fromCurrency){
            $response = ['errors' => ["Invalid Currency!"]];
            return response($response);
        }
        if(!$toCurrency){
            $response = ['errors' => ["Invalid Currency!"]];
            return response($response);
        }

        if($fromCurrency->id == $toCurrency->id){
            $response = ['errors' => ["Same Currency not eligible to exchange!"]];
            return response($response);
        }


        $charge = formatter_money($amount * $money_exchange->percent_charge) / 100;

        $totalBaseAmount = formatter_money($amount + $charge);
        $totalSendAmount = formatter_money(($totalBaseAmount / $fromCurrency->rate));

        $onlySendAmount = formatter_money($amount / $fromCurrency->rate);

        $totalExchangeAmount = formatter_money($onlySendAmount * $toCurrency->rate);

        $result['fromCurrency'] = $fromCurrency;
        $result['toCurrency'] = $toCurrency;
        $result['amount'] = $amount;
        $result['charge'] = $charge;
        $result['totalBaseAmount'] = $charge;
        $result['totalExchangeAmount'] = $totalExchangeAmount;
        $result['exchangeRate'] = formatter_money($toCurrency->rate / $fromCurrency->rate);
        $result['feedBack'] = true;
        $result['url'] = url('/api/exchange/confirm/').'?amount='.$amount.'&fromCurrencyId='.$fromCurrency->id.'&toCurrencyId='.$toCurrency->id.'&charge='.formatter_money($charge).'&getAmount='.formatter_money($totalExchangeAmount);

        return response($result, 200);
    }

    public function exchangeConfirm(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'fromCurrencyId' => 'required',
            'toCurrencyId' => 'required',
            'charge' => 'required',
            'getAmount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }


        $amount = round($request->amount,2);
        $charge =  $request->charge;
        $fromCurrencyId = $request->fromCurrencyId;
        $toCurrencyId =  $request->toCurrencyId;
        $getAmount = $request->getAmount;


        $amountWithCharge = formatter_money($amount + $charge);

        $auth = Auth::user();

        $fromCurrencyWallet = Wallet::with('currency')->where('user_id', $auth->id)->where('wallet_id', $fromCurrencyId)->firstOrFail();

        if ($fromCurrencyWallet->amount >= $amountWithCharge) {
            $fromCurrencyWallet->amount -= $amountWithCharge;
            $fromCurrencyWallet->save();

            $toCurrencyWallet = Wallet::with('currency')->where('user_id', $auth->id)->where('wallet_id', $toCurrencyId)->first();

            $toCurrencyWallet->amount =  formatter_money($toCurrencyWallet->amount + $getAmount);
            $toCurrencyWallet->save();

            $trans = getTrx();

            Trx::create([
                'user_id' => $auth->id,
                'amount' => $amount,
                'main_amo' => formatter_money($fromCurrencyWallet->amount),
                'charge' => $charge,
                'currency_id' => $fromCurrencyWallet->wallet_id,
                'trx_type' => '-',
                'remark' => 'Exchange Money',
                'title' => 'Exchange money ' . $fromCurrencyWallet->currency->code .' to '.$toCurrencyWallet->currency->code,
                'trx' => $trans
            ]);

            Trx::create([
                'user_id' => $auth->id,
                'amount' => $getAmount,
                'main_amo' => formatter_money($toCurrencyWallet->amount),
                'charge' => 0,
                'currency_id' => $toCurrencyWallet->wallet_id,
                'trx_type' => '+',
                'remark' => 'Exchange Money',
                'title' => 'Exchange money ' . $toCurrencyWallet->currency->code. ' from ' .$fromCurrencyWallet->currency->code,
                'trx' => $trans
            ]);


            $xchange['user_id'] = $auth->id;
            $xchange['from_currency_id'] = $fromCurrencyWallet->currency->id;
            $xchange['from_currency_amount'] = $amount;
            $xchange['from_currency_charge'] = $charge;
            $xchange['to_currency_id'] = $toCurrencyWallet->currency->id;
            $xchange['to_currency_amount'] = $getAmount;
            $xchange['trx'] = $trans;
            $xchange['status'] = 1;
            ExchangeMoney::create($xchange);

            notify($auth, $type = 'exchange', [
                'from_amount' => formatter_money($amount),
                'from_currency' => $fromCurrencyWallet->currency->code,
                'from_new_balance' => formatter_money($fromCurrencyWallet->amount),
                'to_amount' => formatter_money($getAmount),
                'to_currency' => $toCurrencyWallet->currency->code,
                'to_new_balance' => $toCurrencyWallet->amount,
                'transaction_id' => $trans,
            ]);

            $response['success'] = "Successfully exchange " . $fromCurrencyWallet->currency->code . " to " . $toCurrencyWallet->currency->code;
            $response['result'] = true;
            return response($response);
        } else {
            $response = ['errors' => ["Sorry, Not enough funds to perform the operation!"]];
            return response($response);
        }
    }

    public function exchangeLog()
    {
        $data['currencyList'] = Currency::where('status',1)->get();
        $transactions =  ExchangeMoney::with('user', 'from_currency', 'to_currency')->where('user_id',Auth::id())->where('status', '!=', 0)->latest()->paginate(10);
        $data['transactions'] = resourcePaginate($transactions, function ($data) use ($transactions) {
            return [
                'date' =>show_datetime($data->created_at),
                'trx' =>strtoupper($data->trx),
                'exchange_from' =>formatter_money($data->from_currency_amount) . ' '.$data->from_currency->code,
                'exchange_to' =>formatter_money($data->to_currency_amount) . ' '.$data->to_currency->code,
                'charge' =>formatter_money($data->from_currency_charge). ' '.$data->from_currency->code
            ];
        });
        return response($data,200);
    }

    public function exchangeLogSearch(Request $request)
    {
        $rules = [
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response(['errors' => $validator->errors()->all(), 422]);
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title =   $currency . ' : ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));


        $query = ExchangeMoney::query();
        $query->with('user', 'from_currency','to_currency')
            ->when($currency, function ($q, $currency) {
                $q->whereHas('from_currency', function ($curr) use ($currency) {
                    $curr->where('code', $currency);
                })
                    ->orWhereHas('to_currency', function ($curr) use ($currency) {
                        $curr->where('code', $currency);
                    });
            })
            ->when($start_date, function ($q, $start_date) {
                $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
            })
            ->when($end_date, function ($q, $end_date) {
                $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
            });
        $transactions = $query->paginate(15);


        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            return [
                'date' =>show_datetime($data->created_at),
                'trx' =>strtoupper($data->trx),
                'exchange_from' =>formatter_money($data->from_currency_amount) . ' '.$data->from_currency->code,
                'exchange_to' =>formatter_money($data->to_currency_amount) . ' '.$data->to_currency->code,
                'charge' =>formatter_money($data->from_currency_charge). ' '.$data->from_currency->code
            ];
        });


        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        $response =[
            'page_title'=>$page_title,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'select_currency'=>$currency,
            'currencyList' => $currencyList,
            'transactions'=>$transactions
        ];
        return response($response, 200);
    }


}
