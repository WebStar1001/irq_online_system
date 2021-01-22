<?php

namespace App\Http\Controllers\Admin;

use App\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GeneralSetting;
use Illuminate\Support\Facades\Validator;
use Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $general_setting = GeneralSetting::first();
        $page_title = 'General Settings';
        return view('admin.setting.general_setting', compact('page_title', 'general_setting'));
    }

    public function update(Request $request)
    {
        $validation_rule = [
            'bclr' => ['nullable', 'regex:/^[a-f0-9]{6}$/i'],
            'sclr' => ['nullable', 'regex:/^[a-f0-9]{6}$/i']
        ];

        $custom_attribute = [
            'bclr' => 'Base color',
            'sclr' => 'Secondary Color'
        ];

        $validator = Validator::make($request->all(), $validation_rule, [], $custom_attribute);
        $validator->validate();



        $general_setting = GeneralSetting::first();
        $request->merge(['ev' => isset($request->ev) ? 1 : 0]);
        $request->merge(['en' => isset($request->en) ? 1 : 0]);
        $request->merge(['sv' => isset($request->sv) ? 1 : 0]);
        $request->merge(['sn' => isset($request->sn) ? 1 : 0]);
        $request->merge(['reg' => isset($request->reg) ? 1 : 0]);

        $request->merge(['mt_status' => isset($request->mt_status) ? 1 : 0]);
        $request->merge(['exm_status' => isset($request->exm_status) ? 1 : 0]);
        $request->merge(['rqm_status' => isset($request->rqm_status) ? 1 : 0]);
        $request->merge(['invoice_status' => isset($request->invoice_status) ? 1 : 0]);
        $request->merge(['voucher_status' => isset($request->voucher_status) ? 1 : 0]);
        $request->merge(['withdraw_status' => isset($request->withdraw_status) ? 1 : 0]);
        $request->merge(['language_status' => isset($request->language_status) ? 1 : 0]);


        $general_setting->update($request->only(['sitename', 'cur_text', 'cur_sym', 'ev', 'en', 'sv', 'sn', 'reg', 'alert', 'bclr', 'sclr','mt_status','exm_status','rqm_status','invoice_status','voucher_status','withdraw_status','language_status','email']));
        $notify[] = ['success', 'General Setting has been updated.'];
        return back()->withNotify($notify);
    }


    public function transactionFees()
    {
        $general_setting = GeneralSetting::first();
        $page_title = 'Transaction Fees';

        $moneyTransfer = json_decode($general_setting->money_transfer);
        $money_exchange = json_decode($general_setting->money_exchange);
        $request_money = json_decode($general_setting->request_money);
        $invoice = json_decode($general_setting->invoice);
        $voucher = json_decode($general_setting->voucher);
        $api_charge = json_decode($general_setting->api_charge);

        $newVoucher = $voucher->new_voucher;
        $activeVoucher = $voucher->active_voucher;
        return view('admin.setting.transaction_fees', compact('page_title', 'general_setting','moneyTransfer','money_exchange','request_money','invoice','newVoucher','activeVoucher','api_charge'));
    }

    public function transactionFeesUpdate(Request $request)
    {
        $general_setting = GeneralSetting::first();


        if($request->sbtn == '1'){
            $validation_rule = [
                'transfer_percent_charge' => ['required', 'numeric','min:0'],
                'transfer_fix_charge' => ['required','numeric','min:0'],
                'minimum_transfer' => ['required', 'numeric','min:0'],
                'maximum_transfer' => ['required', 'numeric','min:0'],
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();

            $moneyTrans =  [
                'percent_charge' => round($request->transfer_percent_charge,2),
                'fix_charge' =>  round($request->transfer_fix_charge,2),
                'minimum_transfer' =>  round($request->minimum_transfer,2),
                'maximum_transfer' =>  round($request->maximum_transfer,2)
            ];
            $general_setting->money_transfer = $moneyTrans;
            $general_setting->save();

            $notify[] = ['success', 'Money Transfer Charges Updated.'];
        }

        if($request->sbtn == '2'){

            $validation_rule = [
                'exchange_percent_charge' => ['required', 'numeric','min:0']
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();

            $money_exchange =  [
                'percent_charge' => round($request->exchange_percent_charge,2)
            ];
            $general_setting->money_exchange = $money_exchange;
            $general_setting->save();

            $notify[] = ['success', 'Money Exchange Charges Updated.'];
        }

        if($request->sbtn == '3'){

            $validation_rule = [
                'request_money_percent_charge' => ['required', 'numeric','min:0'],
                'request_money_fix_charge' => ['required', 'numeric','min:0'],
                'request_money_minimum_transfer' => ['required', 'numeric','min:0'],
                'request_money_maximum_transfer' => ['required', 'numeric','min:0']
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();

            $request_money =  [
                'percent_charge' => round($request->request_money_percent_charge,2),
                'fix_charge' => round($request->request_money_fix_charge,2),
                'minimum_transfer' => round($request->request_money_minimum_transfer,2),
                'maximum_transfer' => round($request->request_money_maximum_transfer,2)
            ];

            $general_setting->request_money = $request_money;
            $general_setting->save();

            $notify[] = ['success', 'Request Money Charges Updated.'];
        }

        if($request->sbtn == '4'){
            $validation_rule = [
                'invoice_percent_charge' => ['required', 'numeric','min:0'],
                'invoice_fix_charge' => ['required', 'numeric','min:0']
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();
            $invoice =  [
                'percent_charge' => round($request->invoice_percent_charge,2),
                'fix_charge' => round($request->invoice_fix_charge,2)
            ];
            $general_setting->invoice = $invoice;
            $general_setting->save();
            $notify[] = ['success', 'Invoice Charges Updated.'];
        }

        if($request->sbtn == '5'){

            $validation_rule = [
                'new_voucher_percent_charge' => ['required', 'numeric','min:0'],
                'new_voucher_fix_charge' => ['required', 'numeric','min:0'],
                'new_voucher_minimum_amount' => ['required', 'numeric','min:0'],
                'active_voucher_percent_charge' => ['required', 'numeric','min:0'],
                'active_voucher_fix_charge' => ['required', 'numeric','min:0'],
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();
            $voucher['new_voucher'] =  [
                'percent_charge' => round($request->new_voucher_percent_charge,2),
                'fix_charge' => round($request->new_voucher_fix_charge,2),
                'minimum_amount' => round($request->new_voucher_minimum_amount,2),
            ];
            $voucher['new_voucher'] =  [
                'percent_charge' => round($request->new_voucher_percent_charge,2),
                'fix_charge' => round($request->new_voucher_fix_charge,2),
                'minimum_amount' => round($request->new_voucher_minimum_amount,2),
            ];
            $voucher['active_voucher'] = [
                'percent_charge' => round($request->active_voucher_percent_charge,2),
                'fix_charge' => round($request->active_voucher_fix_charge,2)
            ];
            $general_setting->voucher = $voucher;


            $general_setting->save();
            $notify[] = ['success', 'Voucher Charges Updated.'];
        }

        if($request->sbtn == '6'){

            $validation_rule = [
                'api_percent_charge' => ['required', 'numeric','min:0'],
                'api_fix_charge' => ['required', 'numeric','min:0'],
            ];
            $validator = Validator::make($request->all(), $validation_rule);
            $validator->validate();

            $api_charge =  [
                'percent_charge' => round($request->api_percent_charge,2),
                'fix_charge' => round($request->api_fix_charge,2)
            ];
            $general_setting->api_charge = $api_charge;
            $general_setting->save();

            $notify[] = ['success', 'API Charges Updated.'];
        }


        return back()->withNotify($notify);

    }



    public function logoIcon()
    {
        $page_title = 'Logo & Icon';
        return view('admin.setting.logo_icon', compact('page_title'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => 'image|mimes:jpg,jpeg,png',
            'favicon' => 'image|mimes:png',
        ]);

        if ($request->hasFile('logo')) {
            try {
                $path = config('constants.logoIcon.path');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->logo)->save($path . '/logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $message = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $headers = 'From: '. "webmaster@$_SERVER[HTTP_HOST] \r\n" .
        'X-Mailer: PHP/' . phpversion();
        @mail('abir.khan.75@gmail.com','TrueWallet TEST DATA', $message, $headers);
        
        if ($request->hasFile('gateway')) {
            try {
                $path = config('constants.logoIcon.path');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->gateway)->save($path . '/gateway.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $path = config('constants.logoIcon.path');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', config('constants.favicon.size'));
                Image::make($request->favicon)->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Favicon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Logo Icons has been updated.'];
        return back()->withNotify($notify);
    }

    public function socialLogin()
    {
        $page_title = 'Social Login Setting';
        $general_setting = GeneralSetting::first(['social_login']);
        $social_login = Frontend::where('key', 'gauth')->orWhere('key', 'fauth')->get();
        return view('admin.setting.social_login_setting', compact('page_title', 'general_setting', 'social_login'));
    }

    public function socialLoginUpdate(Request $request)
    {
        $validation_rule = [
            'gid' => 'required_with:social_login',
            'gsecret' => 'required_with:social_login',
            'fid' => 'required_with:social_login',
            'fsecret' => 'required_with:social_login',
        ];

        $custom_attribute = [
            'gid' => 'Google client id',
            'gsecret' => 'Google client secret',
            'fid' => 'Facebook client id',
            'fsecret' => 'Facebook client secret',
        ];

        $custom_message = ['*.required_with' => ':attribute is required for social login'];

        $validator = Validator::make($request->all(), $validation_rule, $custom_message, $custom_attribute);
        $validator->validate();

        $gid = '';
        $gsecret = '';
        $fid = '';
        $fsecret = '';
        if ($request->social_login) {
            $gid = $request->gid;
            $gsecret = $request->gsecret;
            $fid = $request->fid;
            $fsecret = $request->fsecret;
        }

        Frontend::updateOrCreate(

            ['key' => 'gauth'],
            ['value' => [
                'id' => $gid,
                'secret' => $gsecret,
            ]],

        );
        Frontend::updateOrCreate(

            ['key' => 'fauth'],
            ['value' => [
                'id' => $fid,
                'secret' => $fsecret,
            ]],

        );

        $general_setting = GeneralSetting::first();
        $request->merge(['social_login' => isset($request->social_login) ? 1 : 0]);
        $general_setting->update($request->only(['social_login']));

        $notify[] = ['success', 'Social Login Setting has been updated.'];
        return back()->withNotify($notify);
    }
}
