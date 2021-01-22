<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\Trx;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{
    public function deposit()
    {
        $page_title = 'Deposit History';
        $empty_message = 'No deposit history available.';
        $deposits = Deposit::with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = Deposit::with(['user', 'gateway'])->where(function ($q) use ($search) {
            $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', $search);
            });
        });
        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Deposits Search';
                $deposits = $deposits->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Deposits Search';
                $deposits = $deposits->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Deposits Search';
                $deposits = $deposits->where('status', -2);
                break;
            case 'list':
                $page_title .= 'Deposits History Search';
                break;
        }
        $deposits = $deposits->paginate(config('constants.table.defult'));
        $page_title .= ' - ' . $search;

        return view('admin.deposit_list', compact('page_title', 'search', 'scope', 'empty_message', 'deposits'));
    }

    public function pending()
    {
        $page_title = 'Pending Deposits';
        $empty_message = 'No pending deposits.';
        $deposits = Deposit::where('status', 2)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approved()
    {
        $page_title = 'Approved Deposits';
        $empty_message = 'No approved deposits.';
        $deposits = Deposit::where('status', 1)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Deposits';
        $empty_message = 'No rejected deposits.';
        $deposits = Deposit::where('status', -2)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();
        $deposit->update(['status' => 1]);


        $user = Wallet::with('user','currency')->where('wallet_id', $deposit->currency_id)->where('user_id', $deposit->user_id)->first();
        $user['amount'] = formatter_money(($user['amount'] + $deposit->wallet_amount));
        $user->update();


        Trx::create([
            'user_id' => $deposit->user->id,
            'currency_id' => $user->currency->id,
            'amount' => formatter_money($deposit->wallet_amount),
            'main_amo' => formatter_money($user->amount),
            'charge' => 0,
            'type' => '+',
            'remark' => 'Deposit '. $deposit->method_currency,
            'title' => 'Deposit Via ' . $deposit->gateway_currency()->name,
            'trx' => $deposit->trx
        ]);

        notify($user->user, $type = 'deposit_approve', [
            'amount' => formatter_money($deposit->amount),
            'gateway_currency' => $deposit->method_currency,
            'gateway_name' =>  $deposit->gateway_currency()->name,
            'transaction' =>  $deposit->trx,
        ]);



        $notify[] = ['success', 'Deposit has been approved.'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();
        $deposit->update(['status' => -2]);

        notify($deposit->user, $type = 'deposit_reject', [
            'amount' => formatter_money($deposit->amount),
            'gateway_currency' => $deposit->method_currency,
            'gateway_name' =>  $deposit->gateway_currency()->name,
        ]);


        $notify[] = ['success', 'Deposit has been rejected.'];
        return back()->withNotify($notify);
    }
}
