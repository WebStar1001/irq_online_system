<?php

use Illuminate\Http\Request;

//Route::middleware('auth:api')->get('/user', function () {
//    return request()->user();
//});


Route::group(['middleware' => ['json.response']], function () {


    Route::get('/basic', 'Api\GeneralController@basic')->name('api.basic');
    Route::get('/currency', 'Api\GeneralController@currencyList')->name('api.currency');



    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/mail-code', 'Api\AuthenticateUserController@sendMailCode')->name('user.mail-code');
        Route::post('/mail-verify', 'Api\AuthenticateUserController@mailVerify')->name('user.mail-verify');
        Route::post('/sms-code', 'Api\AuthenticateUserController@sendSmsCode')->name('user.sms-code');
        Route::post('/sms-verify', 'Api\AuthenticateUserController@smsVerify')->name('user.sms-verify');


        Route::group(['middleware' => ['CheckStatusApi']], function () {
            Route::get('/user', 'Api\AuthenticateUserController@user');
            Route::post('/update-profile', 'Api\AuthenticateUserController@updateProfile');
            Route::post('/change-password', 'Api\AuthenticateUserController@changePassword');
            Route::get('/login-history', 'Api\AuthenticateUserController@loginHistory');

            /*
             * User API KEY MANAGE
             */
            Route::get('/api-key', 'Api\AuthenticateUserController@apiKey');
            Route::post('/api-key', 'Api\AuthenticateUserController@apiKeyStore');
            Route::get('/apikey-delete/{id?}', 'Api\AuthenticateUserController@apiKeyDelete');

            Route::get('/home', 'Api\AuthenticateUserController@index')->name('user.home');


            Route::get('/transaction', 'Api\AuthenticateUserController@transaction')->name('user.transaction');
            Route::get('/transaction/search/{currency?}/{start_date?}/{end_date?}', 'Api\AuthenticateUserController@transactionSearch')->name('transaction.search');

            Route::post('/checkValidUser', 'Api\AuthenticateUserController@checkValidUser')->name('check.valid.user');

            /*
             * User Money Transfer
             */
            Route::get('/transfer', 'Api\UserMoneyTransferController@moneyTransfer')->name('user.moneyTransfer');
            Route::post('/transfer', 'Api\UserMoneyTransferController@startTransfer')->name('user.moneyTransfer');
            Route::get('/transfer/preview/{cryptId}', 'Api\UserMoneyTransferController@previewTransfer')->name('api.previewTransfer');
            Route::post('/transfer/confirm', 'Api\UserMoneyTransferController@confirmTransfer')->name('confirm.transfer');
            Route::get('/transfer/sent', 'Api\UserMoneyTransferController@transferOutgoing')->name('sent.transfer');
            Route::get('/transfer/received', 'Api\UserMoneyTransferController@transferIncoming')->name('transferIncoming');
            Route::post('/transfer/release', 'Api\UserMoneyTransferController@transferRelease')->name('transferRelease');

            /*
             * User Exchange
             */
            Route::post('exchange/calculation', 'Api\UserExchangeController@exchangeCalculation')->name('exchange.calculation');
            Route::get('exchange/confirm/{amount?}/{fromCurrencyId?}/{toCurrencyId?}/{charge?}/{getAmount?}', 'Api\UserExchangeController@exchangeConfirm')->name('exchange.confirm');
            Route::get('/exchange-log', 'Api\UserExchangeController@exchangeLog')->name('exchangeLog');
            Route::get('/exchange-log/search/{currency?}/{start_date?}/{end_date?}', 'Api\UserExchangeController@exchangeLogSearch')->name('exchangeLog.search');

            /*
             * User Request Money
             */
            Route::get('request/now', 'Api\UserRequestMoneyController@makeRequestMoney')->name('request-money.create');
            Route::post('request/now', 'Api\UserRequestMoneyController@requestMoneyStore')->name('request-money.store');
            Route::get('request/my-request', 'Api\UserRequestMoneyController@requestMoneySendLog')->name('request-money.sent');
            Route::get('request/to-me', 'Api\UserRequestMoneyController@requestMoney')->name('request-money.inbox');
            Route::post('request/action', 'Api\UserRequestMoneyController@moneyReceivedAction')->name('request-money.action');

            /*
             * User Vouchers
             */
            Route::get('vouchers', 'Api\UserVoucherController@vouchers')->name('vouchers');
            Route::post('vouchers/redeem', 'Api\UserVoucherController@voucherActiveCodePreview')->name('active_code.preview');
            Route::post('voucher/redeem-preview', 'Api\UserVoucherController@voucherSaveCode')->name('voucher.SaveCode');
            Route::get('vouchers/redeem-log', 'Api\UserVoucherController@voucherRedeemLog')->name('vouchers.redeemLog');
            Route::post('vouchers/create', 'Api\UserVoucherController@NewVoucherPreview')->name('vouchers.preview');
            Route::get('vouchers/create_voucher', 'Api\UserVoucherController@createVoucher')->name('vouchers.create_voucher');

            /*
             * User Support Ticket
             */
            Route::get('department', 'Api\UserSupportTicketController@department');
            Route::post('createSupportTicket', 'Api\UserSupportTicketController@storeSupportTicket');
            Route::get('supportTicket', 'Api\UserSupportTicketController@supportTicket');
            Route::get('supportMessage/{ticket}', 'Api\UserSupportTicketController@supportMessage')->name('message');

            Route::get('ticketDownload/{ticket}', 'Api\UserSupportTicketController@ticketDownload')->name('ticket.download');
            Route::get('ticketDelete/{id}', 'Api\UserSupportTicketController@ticketDelete')->name('ticket.delete');
            Route::post('userReplyTicket', 'Api\UserSupportTicketController@supportMessageStore')->name('message.store');


            /*
             * User Withdraw Ticket
             */
            Route::get('/withdraw-money', 'Api\UserWithdrawMoneyController@withdrawMoney');
            Route::post('/withdraw-money', 'Api\UserWithdrawMoneyController@withdrawMoneyRequest');
            Route::get('/withdraw-preview/{trx}', 'Api\UserWithdrawMoneyController@withdrawReqPreview')->name('withdraw.preview');
            Route::post('/withdraw-preview', 'Api\UserWithdrawMoneyController@withdrawReqSubmit')->name('withdraw.submit');
            Route::get('/withdraw-log', 'Api\UserWithdrawMoneyController@withdrawLog');

            /*
             *
             */
            Route::get('invoice', 'Api\UserInvoiceController@index')->name('invoice');
            Route::get('invoice/edit/{trx}', 'Api\UserInvoiceController@invoiceEdit')->name('invoice.edit');
            Route::get('invoice/create', 'Api\UserInvoiceController@invoiceCreate')->name('invoice.create');
            Route::post('invoice/create', 'Api\UserInvoiceController@invoiceStore')->name('invoice.store');

            Route::post('invoice/update', 'Api\UserInvoiceController@invoiceUpdate')->name('invoice.Update');

            Route::get('invoice/sendmail/{invoice_id}', 'Api\UserInvoiceController@invoiceSendToMail')->name('invoice.sendmail');
            Route::get('invoice/cancel/{trx}', 'Api\UserInvoiceController@invoiceCancel')->name('invoice.cancel');
            Route::get('invoice/publish/{trx}', 'Api\UserInvoiceController@invoicePublish')->name('invoice.publish');

            /*
             * User Deposit Log
             */
            Route::get('/deposit-log', 'Api\UserDepositMoneyController@depositLog')->name('depositLog');

            Route::get('deposit', 'Api\UserDepositMoneyController@deposit')->name('api.deposit');
            Route::post('deposit', 'Api\UserDepositMoneyController@deposit')->name('api.deposit');
            Route::post('deposit-insert', 'Api\UserDepositMoneyController@depositInsert')->name('deposit.insert');


            // Manual Payment
            Route::get('manual/deposit/{track}', 'Api\UserDepositMoneyController@manualDepositForm')->name('manualDeposit.form');
            Route::post('manual/depositConfirm', 'Api\UserDepositMoneyController@manualDepositUpdate')->name('manualDeposit.update');
            Route::get('deposit-confirm/{track}', 'Gateway\PaymentController@apiDepositConfirm')->name('deposit-api.confirm');




        });
    });

    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    Route::post('/register', 'Api\AuthRegisterController@register')->name('register.api');


});
