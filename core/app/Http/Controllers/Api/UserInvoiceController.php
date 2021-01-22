<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Invoice;
use Illuminate\Http\Request;
use Auth;
use File;
use Illuminate\Validation\Rule;
use Validator;

class UserInvoiceController extends Controller
{
    public function index()
    {
        $basic = GeneralSetting::first();
        if($basic->invoice_status == 0){
            $response = ['errors' => ["Invoice DeActive By Admin"]];
            return response($response);
        }
        $transactions = Invoice::where('user_id', Auth::id())->latest()->paginate(15);
        $transactions = resourcePaginate($transactions, function ($data) use ($transactions) {
            $bar = ($data->status == 0) ? "Unpaid" : (($data->status == 1)  ? "Paid" : (($data->status == -1)  ? "Canceled" : " "));
            return [
                'id' => $data->id,
                'name' => $data->name,
                'email' => $data->email,
                'amount' => formatter_money($data->amount) . " " . $data->currency->code,
                'publish' => [
                  'status' => $data->published,
                  'level' => ($data->published == 1) ? 'Yes' : 'No',
                ],
                'payment_status' => [
                  'status' => $data->status,
                  'level' => $bar,
                ],
                'edit_url' => route('invoice.edit',$data->trx),
                'download_url' => route('getInvoice.pdf',$data->trx),
                'date' => date('d M, Y', strtotime($data->created_at))
            ];
        });
        return response($transactions, 200);
    }


    public function invoiceCreate()
    {
        $basic = GeneralSetting::first();
        if($basic->invoice_status == 0){
            $response = ['errors' => ["Invoice DeActive By Admin"]];
            return response($response);
        }
        $basic =  GeneralSetting::first();
        $data['currency'] = Currency::whereStatus(1)->get();
        $data['user'] = Auth::user();
        $data['charge'] = json_decode($basic->invoice);

        return response($data);
    }

    public function invoiceStore(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'currency' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }


        $currency =  Currency::where('id', $request->currency)->where('status',1)->first();
        if(!$currency){
            $response = ['errors' => ["Invalid Currency"]];
            return response($response);
        }

        $basic =  GeneralSetting::first();
        $charge = json_decode($basic->invoice);
        $arr_details =  array_combine($request->details, $request->amount);

        $total_amount = formatter_money(array_sum($request->amount));

        if($total_amount <0){
            $response = ['errors' => ["Amount Must Be Positive Number"]];
            return response($response);
        }

        $charge = formatter_money((($total_amount*$charge->percent_charge)/100)+ ($charge->fix_charge * $currency->rate));

        $will_get =  $total_amount -$charge;

        if($will_get < 0){
            $response = ['errors' => ['You Must Be get amount above 0 '.$currency->code]];
            return response($response);
        }

        $trx = getTrx();
        $in['user_id'] =  Auth::id();
        $in['currency_id'] =  $currency->id;
        $in['trx'] = $trx;
        $in['name'] =  $request->name;
        $in['email'] =  strtolower(trim($request->email));
        $in['address'] =  $request->address;
        $in['details'] =  json_encode($arr_details);
        $in['amount'] = formatter_money($total_amount);
        $in['will_get'] = formatter_money($will_get);
        $in['charge'] = formatter_money($charge);

        Invoice::create($in);


        $response['success'] = 'Invoice Create Successfully!';
        $response['url'] = route('invoice.edit',$trx);
        return response($response);
    }



    public function invoiceEdit($trx)
    {
        $basic = GeneralSetting::first();
        if($basic->invoice_status == 0){
            $response = ['errors' => ["Invoice DeActive By Admin"]];
            return response($response);
        }
        $basic = GeneralSetting::first();
        $info =  Invoice::where('trx', $trx)->where('user_id',Auth::id())->latest()->first();
        if(!$info){
            $response = ['errors' => ["Invalid Invoice Request"]];
            return response($response);
        }

        $data['currency'] = Currency::whereStatus(1)->get();
        $data['info_details'] = json_decode($info->details, true);
        $data['charge'] = json_decode($basic->invoice);
        $data['info'] = $info;
        $data['PaymentUrl'] = [
            'status' => ($info->status != -1) ? true :false,
            'url' => route('getInvoice.payment', $info->trx),
        ];
        $data['send_mail'] = [
            'status' => ($info->status != -1) ? true :false,
            'url' => route('invoice.sendmail',$info->id),
        ];
        $data['download_url'] = [
            'status' => ($info->status != -1) ? true :false,
            'url' => route('getInvoice.pdf',$info->trx),
        ];
        $data['cancel_invoice'] = [
            'status' => ($info->status == 0) ? true :false,
            'url' => route('invoice.cancel',$info->id),
            'popup' => true,
        ];

        $data['can_publish'] = [
            'status' => ($info->status != -1 && $info->published == 0) ? true :false,
            'popup' => true,
            'url' => route('invoice.publish',$info->id)
        ];

        $data['can_update'] = [
            'status' => ($info->published == 0 && $info->status == 0) ? true :false,
        ];
        return response($data);
    }

    public function invoiceUpdate(Request $request)
    {
        $rules = [
            'id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'currency' => 'required',
            'published' => ['numeric', Rule::in([0,1])],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $basic = GeneralSetting::first();
        $charge = json_decode($basic->invoice);

        $invoice =  Invoice::where('id',$request->id)->where('user_id',Auth::id())->latest()->first();
        if(!$invoice){
            $response = ['errors' => ['Invalid Invoice']];
            return response($response);
        }

        $currency =  Currency::where('id', $request->currency)->where('status',1)->first();
        if(!$currency){
            $response = ['errors' => ['Invalid Currency']];
            return response($response);
        }

        if($invoice->published == 1)
        {
            $response = ['errors' => ['Unable to update']];
            return response($response);
        }

        $arr_details =  array_combine($request->details, $request->amount);

        $total_amount = formatter_money(array_sum($request->amount));
        $charge = formatter_money((($total_amount*$charge->percent_charge)/100)+ ($charge->fix_charge * $currency->rate));
        $will_get =  $total_amount -$charge;


        if($will_get < 0){
            $response = ['errors' => ['You Must Be get amount above 0 '.$currency->code]];
            return response($response);
        }


        $in['user_id'] =  Auth::id();
        $in['currency_id'] = $currency->id;
        $in['name'] =  $request->name;
        $in['email'] =  strtolower(trim($request->email));
        $in['address'] =  $request->address;
        $in['published'] =  ($request->published == 1) ? 1 : 0;
        $in['details'] =  json_encode($arr_details);

        $in['amount'] = formatter_money($total_amount);
        $in['will_get'] = formatter_money($will_get);
        $in['charge'] = formatter_money($charge);

        $invoice->update($in);

        $response['success'] = 'Invoice Update Successfully!';
        return response($response);
    }



    public function invoiceSendToMail($id)
    {
        $data = Invoice::findOrFail($id);

        $payUrl = route('getInvoice.payment', $data->trx);
        $downloadUrl = route('getInvoice.pdf', $data->trx);
        send_invoice($data, $type = 'invoice-create', [
            'amount' => formatter_money($data->amount),
            'currency' => $data->currency->code,
            'creator_email' => $data->user->email,
            'payment_link' => $payUrl,
            'download_link' => $downloadUrl,
        ]);

        $response['success'] = "Mail Send successfully";
        $response['result'] = true;
        return response($response);
    }

    public function invoiceCancel($id)
    {
        $data = Invoice::where('user_id',Auth::id())->where('id',$id)->first();
        if(!$data){
            $response = ['errors' => ["Invalid Request!"]];
            return response($response);
        }
        if($data->status == 0)
        {
            $data->status = -1; //cancel
            $data->update();
            $response['success'] = 'Invoice  Cancel Successfully!';
            return response($response);
        }

        $response = ['errors' => ["Unable to Cancel Invoice!"]];
        return response($response);
    }

    public function invoicePublish($id)
    {
        $data = Invoice::where('user_id',Auth::id())->where('id',$id)->first();
        if(!$data){
            $response = ['errors' => ["Invalid Request!"]];
            return response($response);
        }
        if($data->published == 0)
        {
            $data->published = 1; //published
            $data->update();
            $response['success'] = 'Invoice published Successfully!';
            return response($response);
        }

        $response = ['errors' => ["Unable to published Invoice!"]];
        return response($response);
    }
}
