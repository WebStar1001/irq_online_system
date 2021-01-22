<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\ExchangeMoney;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\MoneyTransfer;
use App\RequestMoney;
use App\Trx;
use App\Voucher;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction()
    {
        $page_title = 'Transaction Logs';
        $transactions = Trx::with('user', 'currency')->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }

    public function transactionSearch(Request $request)
    {
        $request->validate(['search' => 'sometimes|required']);
        $search = $request->search;
        $page_title = 'Transactions Search - ' . $search;
        $empty_message = 'No transactions.';
        $transactions = Trx::with('user', 'currency')->whereHas('user', function ($user) use ($search) {
            $user->where('username', $search);
        })->orWhere('trx', $search)->paginate(config('constants.table.default'));
        return view('admin.reports.transactions', compact('page_title', 'transactions', 'empty_message'));
    }


    public function moneyTransfer()
    {
        $page_title = 'Money Transfer Logs';
        $transactions = MoneyTransfer::with('sender', 'currency', 'receiver')->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No Data Found.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.money-transfer', compact('page_title', 'transactions', 'empty_message', 'currencyList'));
    }

    public function moneyTransferSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = 'Money Transfer Search - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
        $empty_message = 'No Data Found!.';

        $query = MoneyTransfer::query();
        $query->with('sender', 'currency', 'receiver')->where('status', '!=', 0)
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

        return view('admin.reports.money-transfer', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency'));
    }


    public function moneyExchange()
    {
        $page_title = 'Money Exchange Logs';
        $transactions = ExchangeMoney::with('user', 'from_currency', 'to_currency')->where('status', '!=', 0)->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.money-exchange', compact('page_title', 'transactions', 'empty_message', 'currencyList'));
    }

    public function moneyExchangeSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = 'Money Exchange Search - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
        $empty_message = 'No Data Found!.';

        $query = ExchangeMoney::query();
        $query->with('user', 'from_currency', 'to_currency')->where('status', '!=', 0)
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

        return view('admin.reports.money-exchange', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency'));
    }


    public function moneyRequest()
    {
        $page_title = 'Request Money  Logs';
        $transactions = RequestMoney::with('user', 'currency', 'receiver')->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.money-request', compact('page_title', 'transactions', 'empty_message', 'currencyList'));
    }

    public function moneyRequestSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = 'Request Money  Search - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
        $empty_message = 'No Data Found!.';

        $query = RequestMoney::query();
        $query->with('user', 'currency', 'receiver')
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

        return view('admin.reports.money-request', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency'));
    }
    public function voucherGift()
    {
        $page_title = 'Voucher Logs';
        $transactions = Voucher::with('user', 'creator', 'currency')->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.voucher-gift', compact('page_title', 'transactions', 'empty_message', 'currencyList'));
    }

    public function voucherGiftSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = 'Voucher  Search - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
        $empty_message = 'No Data Found!.';

        $query = Voucher::query();
        $query->with('user', 'creator', 'currency')
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

        return view('admin.reports.voucher-gift', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency'));
    }



    public function invoice()
    {
        $page_title = 'Invoice Logs';
        $transactions = Invoice::with('user', 'currency')->latest()->paginate(config('constants.table.default'));
        $empty_message = 'No transactions.';
        $currencyList = Currency::where('status', 1)->orderBy('code', 'asc')->get();
        return view('admin.reports.invoice-log', compact('page_title', 'transactions', 'empty_message', 'currencyList'));
    }

    public function invoiceSearch(Request $request)
    {
        $request->validate([
            'start_date' => 'sometimes|required|date_format:d-m-Y',
            'end_date' => 'sometimes|required|date_format:d-m-Y',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $currency = $request->currency;

        $page_title = 'Invoice Search - ' . $currency . ' : ' . date('d M, Y', strtotime($start_date)) . ' - ' . date('d M, Y', strtotime($end_date));
        $empty_message = 'No Data Found!.';

        $query = Invoice::query();
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

        return view('admin.reports.invoice-log', compact('page_title', 'transactions', 'empty_message', 'currencyList', 'start_date', 'end_date', 'currency'));
    }



}
