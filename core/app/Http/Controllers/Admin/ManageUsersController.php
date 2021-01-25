<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Docver;
use App\ExchangeMoney;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\MoneyTransfer;
use App\RequestMoney;
use App\Trx;
use App\User;
use App\UserLogin;
use App\Voucher;
use App\Wallet;
use Illuminate\Http\Request;
use PhpParser\Comment\Doc;

class ManageUsersController extends Controller
{
    public function allUsers()
    {
        $page_title = 'Manage Users';
        $empty_message = 'No user found';
        $users = User::orderBy('firstname')->orderBy('lastname')->paginate(config('constants.table.default'));
        return view('admin.users.users', compact('page_title', 'empty_message', 'users'));
    }

    public function activeUsers()
    {
        $page_title = 'Manage Active Users';
        $empty_message = 'No active user found';
        $users = User::active()->orderBy('firstname')->orderBy('lastname')->paginate(config('constants.table.default'));
        return view('admin.users.users', compact('page_title', 'empty_message', 'users'));
    }

    public function bannedUsers()
    {
        $page_title = 'Manage Banned Users';
        $empty_message = 'No banned user found';
        $users = User::banned()->orderBy('firstname')->orderBy('lastname')->paginate(config('constants.table.default'));
        return view('admin.users.users', compact('page_title', 'empty_message', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $page_title = 'Manage Email Unverified Users';
        $empty_message = 'No email unverified user found';
        $users = User::emailUnverified()->orderBy('firstname')->orderBy('lastname')->paginate(config('constants.table.default'));
        return view('admin.users.users', compact('page_title', 'empty_message', 'users'));
    }

    public function smsUnverifiedUsers()
    {
        $page_title = 'Manage SMS Unverified Users';
        $empty_message = 'No sms unverified user found';
        $users = User::smsUnverified()->orderBy('firstname')->orderBy('lastname')->paginate(config('constants.table.default'));
        return view('admin.users.users', compact('page_title', 'empty_message', 'users'));
    }

    public function documentRequest()
    {

        $page_title = 'Manage Document Verify';
        $empty_message = 'No document verify found';

        $docs = Docver::orderBy('id', 'desc')->paginate(10);

        return view('admin.users.document', compact('page_title', 'empty_message', 'docs'));
    }

    public function documentApprove(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user['docv'] = $request->docv == "1" ? 1 : 0;

        $user->save();

        Docver::where('user_id', $id)->update(array('is_ver' => $request->docv == "1" ? 1 : 0));

        $msg = 'Your Document Verified Successfully';
        send_email($user->email, $user->username, 'Document Verified', $msg);
        $sms = 'Your Document Verified Successfully';
        send_sms($user->mobile, $sms);


        return back()->withSuccess('Document Verification Successful');

    }

    public function detail($id)
    {
        $user = User::findOrFail($id);
        $userWallet = Wallet::with('currency')->where('user_id', $id)->get();
        $withdrawals = $user->withdrawals()->count();
        $deposits = $user->deposits()->count();
        $transactions = $user->transactions()->count();
        $docs = Docver::orderBy('id', 'desc')->where('user_id', $id)->get();
        $page_title = 'User Detail';
        return view('admin.users.detail', compact('page_title', 'user', 'withdrawals', 'deposits', 'transactions', 'userWallet', 'docs'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where(function ($user) use ($search) {
            $user->where('username', $search)->orWhere('email', $search)->orWhere('account_number', $search);
        });
        $page_title = '';
        switch ($scope) {
            case 'active':
                $page_title .= 'Active ';
                $users = $users->where('status', 1);
                break;
            case 'banned':
                $page_title .= 'Banned';
                $users = $users->where('status', 0);
                break;
            case 'emailUnverified':
                $page_title .= 'Email Unerified ';
                $users = $users->where('ev', 0);
                break;
            case 'smsUnverified':
                $page_title .= 'SMS Unverified ';
                $users = $users->where('sv', 0);
                break;
        }
        $users = $users->paginate(config('constants.table.default'));
        $page_title .= 'User Search - ' . $search;
        $empty_message = 'No search result found';
        return view('admin.users.users', compact('page_title', 'search', 'scope', 'empty_message', 'users'));
    }

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $request->validate([
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
            'email' => 'required|email|max:160|unique:users,email,' . $user->id,
        ]);


        if ($request->email != $user->email && User::whereEmail($request->email)->whereId('!=', $user->id)->count() > 0) {
            $notify[] = ['error', 'Email already exists.'];
            return back()->withNotify($notify);
        }

        if ($request->mobile != $user->mobile && User::where('mobile', $request->mobile)->whereId('!=', $user->id)->count() > 0) {
            $notify[] = ['error', 'Phone number already exists.'];
            return back()->withNotify($notify);
        }

        $user->update([
            'mobile' => $request->mobile,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $request->country,
            ],
            'status' => $request->status ? 1 : 0,
            'ev' => $request->ev ? 1 : 0,
            'sv' => $request->sv ? 1 : 0,
            'docv' => $request->docv ? 1 : 0,
            'ts' => $request->ts ? 1 : 0,
            'tv' => $request->tv ? 1 : 0,
        ]);

        $notify[] = ['success', 'User detail has been updated'];
        return redirect()->route('admin.users.detail', $user->id)->withNotify($notify);
    }

    public function loginHistory(Request $request)
    {

        if ($request->search) {
            $search = $request->search;
            $page_title = 'User Login History Search - ' . $search;
            $empty_message = 'No search result found.';
            $login_logs = UserLogin::whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->latest()->paginate(config('constants.table.default'));
            return view('admin.users.logins', compact('page_title', 'empty_message', 'search', 'login_logs'));
        }
        $page_title = 'User Login History';
        $empty_message = 'No users login found.';
        $login_logs = UserLogin::latest()->paginate(config('constants.table.default'));
        return view('admin.users.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function userLoginHistory($id)
    {
        $user = User::findOrFail($id);
        $page_title = 'User Login History - ' . $user->username;
        $empty_message = 'No users login found.';
        $login_logs = $user->login_logs()->latest()->paginate(config('constants.table.default'));
        return view('admin.users.logins', compact('page_title', 'empty_message', 'login_logs'));
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required',
        ]);

        $userWallet = Wallet::with('currency', 'user')->where('id', $request->currency)->where('user_id', $id)->firstOrFail();


        $user = User::findOrFail($id);
        $amount = formatter_money($request->amount);


        if ($request->act) {
            $userWallet->amount = bcadd($userWallet->amount, $amount, site_precision());
            $userWallet->save();

            $trx = getTrx();
            Trx::create([
                'user_id' => $user->id,
                'currency_id' => $userWallet->currency->id,
                'amount' => $amount,
                'main_amo' => formatter_money($userWallet->amount),
                'charge' => 0,
                'type' => '+',
                'remark' => 'Added Balance By Admin',
                'title' => $amount . ' ' . $userWallet->currency->code . ' has been added By Admin ',
                'trx' => $trx
            ]);

            notify($user, $type = 'admin-add-balance', [
                'amount' => $amount,
                'currency' => $userWallet->currency->code,
                'remaining_balance' => formatter_money($userWallet->amount),
                'transaction' => $trx,
            ]);
            $notify[] = ['success', $amount . ' ' . $userWallet->currency->code . ' has been added to ' . $user->username . ' balance'];
        } else {
            if ($amount > $userWallet->amount) {
                $notify[] = ['error', $user->username . ' has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $userWallet->amount = bcsub($userWallet->amount, $amount, site_precision());
            $userWallet->save();

            $trx = getTrx();

            Trx::create([
                'user_id' => $user->id,
                'currency_id' => $userWallet->currency->id,
                'amount' => $amount,
                'main_amo' => formatter_money($userWallet->amount),
                'charge' => 0,
                'type' => '-',
                'remark' => 'Subtract Balance By Admin',
                'title' => $amount . ' ' . $userWallet->currency->code . ' has been subtracted By Admin ',
                'trx' => $trx
            ]);

            notify($user, $type = 'admin-sub-balance', [
                'amount' => $amount,
                'currency' => $userWallet->currency->code,
                'remaining_balance' => formatter_money($userWallet->amount),
                'transaction' => $trx,
            ]);

            $notify[] = ['success', $amount . ' ' . $userWallet->currency->code . ' has been subtracted from ' . $user->username . ' balance'];
        }

        return back()->withNotify($notify);
    }

    public function showEmailAllForm()
    {
        $page_title = 'Send Email To All Users';
        return view('admin.users.email_all', compact('page_title'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (User::where('status', 1)->cursor() as $user) {
            send_general_email($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All users will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function showEmailSingleForm($id)
    {
        $user = User::findOrFail($id);
        $page_title = 'Send Email To: ' . $user->username;

        return view('admin.users.email_single', compact('page_title', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = User::findOrFail($id);
        send_general_email($user->email, $request->subject, $request->message, $user->username);

        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function withdrawals(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $page_title = 'Search User Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', $search)->latest()->paginate(config('table.default'));
            $empty_message = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('page_title', 'user', 'search', 'withdrawals', 'empty_message'));
        }
        $page_title = 'User Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->latest()->paginate(config('table.default'));
        $empty_message = 'No withdrawals';
        return view('admin.withdraw.withdrawals', compact('page_title', 'user', 'withdrawals', 'empty_message'));
    }

    public function deposits(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $page_title = 'Search User Deposits : ' . $user->username;
            $deposits = $user->deposits()->where('trx', $search)->latest()->paginate(config('table.default'));
            $empty_message = 'No deposits';
            return view('admin.deposit_list', compact('page_title', 'search', 'user', 'deposits', 'empty_message'));
        }

        $page_title = 'User Deposit : ' . $user->username;
        $deposits = $user->deposits()->latest()->paginate(config('table.default'));
        $empty_message = 'No deposits';
        return view('admin.deposit_list', compact('page_title', 'user', 'deposits', 'empty_message'));
    }

    public function transactions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $page_title = 'Search User Transactions : ' . $user->username;
            $transactions = $user->transactions()->where('trx', $search)->latest()->paginate(config('table.default'));
            $empty_message = 'No transactions';
            return view('admin.reports.transactions', compact('page_title', 'search', 'user', 'transactions', 'empty_message'));
        }
        $page_title = 'User Transactions : ' . $user->username;
        $transactions = $user->transactions()->latest()->paginate(config('table.default'));
        $empty_message = 'No transactions';
        return view('admin.reports.transactions', compact('page_title', 'user', 'transactions', 'empty_message'));
    }


    public function moneyTransfer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        if (isset($request->currency)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $currency = $request->currency;
            $page_title = $user->username . ' Money Transfer  - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
            $empty_message = 'No Data Found!.';

            $query = MoneyTransfer::query();
            $query->with('sender', 'currency', 'receiver')->where('status', '!=', 0)->where('sender_id', $id)
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

            return view('admin.reports.user.money-transfer', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency', 'user'));
        }


        $page_title = 'Money Transfer : ' . $user->username;
        $transactions = MoneyTransfer::with('sender', 'currency', 'receiver')->where('sender_id', $id)->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.user.money-transfer', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'user'));
    }


    public function moneyExchange(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        if (isset($request->currency)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $currency = $request->currency;

            $page_title = $user->username . ' Money Exchange  - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
            $empty_message = 'No Data Found!.';

            $query = ExchangeMoney::query();
            $query->with('user', 'from_currency', 'to_currency')->where('status', '!=', 0)->where('user_id', $id)
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

            return view('admin.reports.user.money-exchange', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency', 'user'));
        }


        $page_title = 'Money Exchange : ' . $user->username;
        $transactions = ExchangeMoney::with('user', 'from_currency', 'to_currency')->where('user_id', $id)->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.user.money-exchange', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'user'));
    }


    public function moneyRequest(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        if (isset($request->currency)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $currency = $request->currency;

            $page_title = $user->username . ' Money Request  - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
            $empty_message = 'No Data Found!.';

            $query = RequestMoney::query();
            $query->with('user', 'currency', 'receiver')->where('sender_id', $user->id)
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
            return view('admin.reports.user.money-request', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency', 'user'));
        }

        $page_title = 'Money Request : ' . $user->username;
        $transactions = RequestMoney::with('user', 'currency', 'receiver')->where('sender_id', $user->id)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.user.money-request', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'user'));
    }


    public function voucherLog(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        if (isset($request->currency)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $currency = $request->currency;

            $page_title = $user->username . ' Voucher  - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
            $empty_message = 'No Data Found!.';

            $query = Voucher::query();
            $query->with('user', 'creator', 'currency')->where('user_id', $user->id)
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
            return view('admin.reports.user.voucher-gift', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency', 'user'));
        }

        $page_title = 'Voucher : ' . $user->username;
        $transactions = Voucher::with('user', 'creator', 'currency')->where('user_id', $user->id)->latest()->paginate(config('constants.table.default'));

        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.user.voucher-gift', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'user'));
    }


    public function invoiceLog(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        if (isset($request->currency)) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $currency = $request->currency;

            $page_title = $user->username . ' Invoices  - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
            $empty_message = 'No Data Found!.';

            $query = Invoice::query();
            $query->with('user', 'currency')->where('user_id', $user->id)
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
            return view('admin.reports.user.invoice-log', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency', 'user'));
        }

        $page_title = 'Invoices : ' . $user->username;
        $transactions = Invoice::with('user', 'currency')->where('user_id', $user->id)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.user.invoice-log', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'user'));
    }


}
