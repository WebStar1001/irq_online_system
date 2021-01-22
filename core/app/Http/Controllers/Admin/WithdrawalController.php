<?php

namespace App\Http\Controllers\Admin;

use App\Trx;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Withdrawal;

class WithdrawalController extends Controller
{
    public function pending()
    {
        $page_title = 'Pending Withdrawals';
        $withdrawals = Withdrawal::where('status', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is pending';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function approved()
    {
        $page_title = 'Approved Withdrawals';
        $withdrawals = Withdrawal::where('status', 1)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is approved';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Withdrawals';
        $withdrawals = Withdrawal::where('status', 2)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal is rejected';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function log()
    {
        $page_title = 'Withdrawal History';
        $withdrawals = Withdrawal::latest()->paginate(config('constants.table.default'));
        $empty_message = 'No withdrawal history';
        return view('admin.withdraw.withdrawals', compact('page_title', 'withdrawals', 'empty_message'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id',$request->id)->where('status',0)->firstOrFail();
        $withdraw->update(['status' => 1]);

        notify($withdraw->user, $type = 'withdraw_approve', [
            'amount' => formatter_money($withdraw->amount),
            'currency' => $withdraw->curr->code,
            'method' =>  $withdraw->method->name,
            'transaction' =>  $withdraw->trx,
        ]);

        $notify[] = ['success', 'Withdrawal has been approved.'];
        return redirect()->route('admin.withdraw.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id',$request->id)->where('status',0)->firstOrFail();
        $withdraw->update(['status' => 2]);


        $userWallet = Wallet::where('id', $withdraw->wallet_id)->first();
        $userWallet->amount += formatter_money($withdraw->amount);
        $userWallet->save();

        $tr = getTrx();
        Trx::create([
            'user_id' => $withdraw->user_id,
            'amount' => formatter_money($withdraw->amount),
            'main_amo' => formatter_money($userWallet->amount),
            'charge' => 0,
            'type' => '+',
            'currency_id' => $withdraw->currency_id,
            'remark' => 'Withdraw Refund',
            'title' => formatter_money($withdraw->amount) . ' ' . $userWallet->currency->code . ' Refunded ',
            'trx' => $tr,
        ]);

        notify($withdraw->user, $type = 'withdraw_reject', [
            'amount' => formatter_money($withdraw->amount),
            'currency' => $userWallet->currency->code,
            'main_balance' => formatter_money($userWallet->amount),
            'method' =>  $withdraw->method->name,
            'transaction' =>  $tr,
        ]);



        $notify[] = ['success', 'Withdrawal has been rejected.'];
        return redirect()->route('admin.withdraw.pending')->withNotify($notify);
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $page_title = '';
        $empty_message = 'No search result found.';

        $withdrawals = Withdrawal::with(['user', 'method'])->where(function ($q) use ($search) {
            $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', $search);
            });
        });

        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 0);
                break;
            case 'approved':
                $page_title .= 'Approved Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Withdrawal Search';
                $withdrawals = $withdrawals->where('status', 2);
                break;
            case 'log':
                $page_title .= 'Withdrawal History Search';
                break;
        }

        $withdrawals = $withdrawals->paginate(config('constants.table.defult'));
        $page_title .= ' - ' . $search;


        return view('admin.withdraw.withdrawals', compact('page_title', 'empty_message', 'search', 'scope', 'withdrawals'));
    }
}
