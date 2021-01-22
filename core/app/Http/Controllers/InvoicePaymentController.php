<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Frontend;
use App\GatewayCurrency;
use Illuminate\Http\Request;
use App\GeneralSetting;
use App\Invoice;
use App\Currency;
use App\Wallet;
use App\User;
use App\Trx;
use PDF;
use Crypt;
use Session;
use Auth;
use Validator;



class InvoicePaymentController extends Controller
{
    public function getInvoicePdf($trx)
    {
        $basic = GeneralSetting::first();
        $page_title   = "Invoice";
        $data = Invoice::with('currency','user')->where('trx', $trx)->firstOrfail();
        $makePdf['invoice_details'] = json_decode($data->details,true);
        $makePdf['info'] = $data;
        $makePdf['contact'] =  Frontend::where('key','contact')->firstOrFail();
        $pdf = PDF::loadView('generatePDF', $makePdf,compact('page_title','basic'));
        return $pdf->download('invoice.pdf');
    }

    public function getInvoicePayment($trx)
    {
        $basic = GeneralSetting::first();
        $invoice = Invoice::where('trx', $trx)->latest()->firstOrFail();
        if($invoice->published == 0){
            return redirect()->route('invoice.initiate.error')->with('error', 'Invoice not Published!!');
        }
        $data['invoice_details'] = json_decode($invoice->details);
        $data['page_title'] = "Invoice Payment";
        $data['gateways'] = GatewayCurrency::with('method')->where('method_code','<',1000)->orderBy('method_code')->get();

        Session::put('invoiceTrx', $invoice->trx);

        return view(activeTemplate().'invoicePayment.preview',$data,compact('invoice'));
    }

    public function initiateError()
    {
        $data['page_title'] = "Error";
        return view(activeTemplate().'invoicePayment.invoice-error',$data);
    }

    public function invoicePreviewToConfirm($trx)
    {
        if(!Auth::user()){
            return redirect()->route('invoice.initiate.error')->with('error', 'Unauthinticate User!!');
        }

        $basic = GeneralSetting::first();
        $track = Session::get('invoiceTrx');

        $invoicePayment =   Invoice::where('trx',decrypt($trx))->where('status',0)->latest()->first();


        if (isset($invoicePayment) && $track)
        {
            $page_title = "Pay with $basic->sitename";
            $invoice =  $invoicePayment;
            $invoice_details = json_decode($invoicePayment->details);
            return view(activeTemplate().'invoicePayment.wallet-pay-preview', compact('invoicePayment','invoice','invoice_details','page_title'));
        }
        return redirect()->route('invoice.initiate.error')->with('error', 'Opps! Something Wrong!!');
    }



    public function payToWalletConfirm(Request $request,  $id)
    {
        if(!Auth::user()){
            return redirect()->route('invoice.initiate.error')->with('error', 'Unauthenticate User!!');
        }
        $basic = GeneralSetting::first();
        $auth = Auth::user();

        $invoicePay = Invoice::where('id',decrypt($id))->where('status',0)->first();



        if(isset($invoicePay)){
            $amount = $invoicePay->amount;
            $getCurrency = $invoicePay->currency_id;
            $paytoname = $invoicePay->user->username;

            $currency = Currency::where('id', $getCurrency)->firstOrFail();

            $userWallet = Wallet::where('user_id',Auth::id())->where('wallet_id', $currency->id)->first();
            if (!$userWallet) {
                return redirect()->route('invoice.initiate.error')->with('error', 'Your Wallet Not Found!!');
            }

            $merchant = User::findOrFail($invoicePay->user_id);
            if (!$merchant) {
                return redirect()->route('invoice.initiate.error')->with('error', 'Invalid Merchant!!');
            }

            if ($merchant->status != 1) {
                return redirect()->route('invoice.initiate.error')->with('error', 'Merchant not allowed to this payment received !!');
            }
            $merchantWallet = Wallet::where('user_id', $merchant->id)->where('wallet_id', $currency->id)->first();

            if ($userWallet->amount >= $amount) {
                // debit from user wallet balance
                $userWallet->amount = formatter_money($userWallet->amount - $amount);
                $userWallet->update();

                // credit to merchant wallet balance
                $merchantWallet->amount = formatter_money($merchantWallet->amount + $invoicePay->will_get);
                $merchantWallet->update();

                $str_random = $invoicePay->trx;

                $invoicePay->paidby_id = Auth::id();
                $invoicePay->gateway = $basic->sitename. ' wallet';
                $invoicePay->status = 1;
                $invoicePay->update();

                //Trx  for merchant
                Trx::create([
                    'user_id' => $merchantWallet->user->id,
                    'amount' => formatter_money($invoicePay->will_get),
                    'main_amo' => formatter_money($merchantWallet->amount),
                    'charge' => formatter_money($invoicePay->charge),
                    'currency_id' => $merchantWallet->currency->id,
                    'type' => '+',
                    'remark' => 'Invoice Payment Confirm ',
                    'title' => 'Invoice Payment From '. $userWallet->user->email,
                    'trx' => $str_random,
                ]);


                //Trx  for user
                $trxUser = Trx::create([
                    'user_id' => $userWallet->user->id,
                    'amount' => $amount,
                    'main_amo' => formatter_money($userWallet->amount),
                    'charge' => 0,
                    'currency_id' => $userWallet->currency->id,
                    'type' => '-',
                    'remark' => 'Invoice Payment Paid',
                    'title' => 'Invoice Payment To  ' . $merchantWallet->user->email,
                    'trx' => $str_random,
                ]);

                send_email($userWallet->user, $type = 'invoice-payment-send', [
                    'amount' => formatter_money($invoicePay->amount),
                    'currency' => $invoicePay->currency->code,
                    'gateway' => $invoicePay->gateway,
                    'invoice_no' => $invoicePay->trx,
                    'receiver_email' => $merchantWallet->user->email,
                    'download_link' => route('getInvoice.pdf', $invoicePay->trx),
                ]);

                send_email($merchantWallet->user, $type = 'invoice-payment-get', [
                    'amount' => formatter_money($invoicePay->will_get),
                    'currency' => $merchantWallet->currency->code,
                    'gateway' => $invoicePay->gateway,
                    'invoice_no' => $invoicePay->trx,
                    'sender_email' => $userWallet->user->email,
                    'download_link' => route('getInvoice.pdf', $invoicePay->trx),
                ]);


                session()->forget('invoiceTrx');

                return redirect()->route('invoice.initiate.error')->with('success', 'Payment Successfully Completed.');

            } else {
                return redirect()->route('invoice.initiate.error')->with('error', 'Insufficient  Balance !!');
            }
        }else{
            return redirect()->route('invoice.initiate.error')->with('error', 'Opps! Something Wrong!!');
        }
    }




    public function invoicePreviewPayment($encrypt_id)
    {
        $transaction =  Session::get('invoiceTrx');
        $basic = GeneralSetting::first();
        $gatewayId = decrypt($encrypt_id);
        $gate = GatewayCurrency::findOrFail($gatewayId);
        $invoicePayment = Invoice::where('trx', $transaction)->where('status', 0)->latest()->first();




        if (isset($invoicePayment)) {

            if (isset($gate)) {

                $storeCurrencyAmo =  formatter_money((1/$gate->rate)/$invoicePayment->currency->rate * $invoicePayment->amount);
                $gatewayCharge = $gate->fixed_charge  + ($storeCurrencyAmo  * $gate->percent_charge / 100);



                $final_amo = formatter_money($storeCurrencyAmo + $gatewayCharge);

                $depo['currency_id'] = $invoicePayment->currency->id;
                $depo['wallet_amount'] = formatter_money($invoicePayment->will_get);

                $depo['user_id'] = Auth::id();
                $depo['method_code'] = $gate->method_code;
                $depo['method_currency'] = strtoupper($gate->currency);
                $depo['amount'] = $storeCurrencyAmo;

                $depo['charge'] = $gatewayCharge;
                $depo['gate_rate'] = $gate->rate;
                $depo['cur_rate'] = $invoicePayment->currency->rate;
                $depo['final_amo'] = formatter_money($final_amo);

                $depo['btc_amo'] = 0;
                $depo['btc_wallet'] = "";
                $depo['trx'] = getTrx();
                $depo['invoice_id'] = $invoicePayment->id;
                $depo['try'] = 0;
                $depo['status'] = 0;
                $ddd = Deposit::create($depo);


                Session::put('Track', $depo['trx']);

                Session::put('invoiceTrx', $invoicePayment->trx);

                return redirect()->route('invoice.deposit-preview');

            }
            return redirect()->route('invoice.initiate.error')->with('error', "Invalid Gateway!");
        }else{
            return redirect()->route('invoice.initiate.error')->with('error', "Invalid Invoice!");
        }

        abort(404);
    }


    public function invoiceDepositPreview()
    {
        $track = Session::get('Track');
        $invoiceTrx = Session::get('invoiceTrx');
        $invoice = Invoice::where('trx', $invoiceTrx)->where('status', 0)->latest()->first();
        if(!$invoice){
            return redirect()->route('invoice.initiate.error')->with('error', 'Invalid Invoice!');
        }

        $data = Deposit::where('status', 0)->where('trx', $track)->latest()->first();

        $page_title = "Payment Preview";
        if(isset($data))
        {
            $invoice_details = json_decode($invoice->details);

            return view(activeTemplate().'invoicePayment.deposit-preview', compact('data', 'page_title','invoice','invoice_details'));
        }
        return redirect()->route('invoice.initiate.error')->with('error', 'Opps! Something Wrong!!');
    }


    public function invoiceSignIn(Request $request)
    {
        $rules = ['username' => 'required', 'password' => 'required'];



        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return response()->json(['fail' => true, 'errors' => $validator->errors()]);
        $data['page_title'] = "Payment Preview";

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            $track = Session::get('invoiceTrx');
            $url = route('invoice.preview.confirm',encrypt($track));
            return response()->json(['url' => $url, 'status'=>'authenticate']);
        } else {
            return response()->json(['msg' => "Username or Password Don't match", 'status'=>'credential']);
        }
    }



}

