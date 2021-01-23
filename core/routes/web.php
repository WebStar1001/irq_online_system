<?php

Route::fallback(function () {
    return view('errors.404');
});

Route::get('/cron-rate', 'FrontendController@cronRate')->name('cron-rate');

Route::get('/api-documentation', 'FrontendController@documentation')->name('documentation');

Route::post('ipn/g101', 'Gateway\g101\ProcessController@ipn')->name('ipn.g101'); // paypal
Route::post('ipn/g102', 'Gateway\g102\ProcessController@ipn')->name('ipn.g102'); // Perfect Money
Route::post('ipn/g103', 'Gateway\g103\ProcessController@ipn')->name('ipn.g103'); // Stripe
Route::post('ipn/g104', 'Gateway\g104\ProcessController@ipn')->name('ipn.g104'); // Skrill
Route::post('ipn/g105', 'Gateway\g105\ProcessController@ipn')->name('ipn.g105'); // PayTM
Route::post('ipn/g106', 'Gateway\g106\ProcessController@ipn')->name('ipn.g106'); // Payeer
Route::post('ipn/g107', 'Gateway\g107\ProcessController@ipn')->name('ipn.g107'); // PayStack
Route::post('ipn/g108', 'Gateway\g108\ProcessController@ipn')->name('ipn.g108'); // VoguePay
Route::get('ipn/g109/{trx}/{type}', 'Gateway\g109\ProcessController@ipn')->name('ipn.g109'); //flutterwave

Route::post('ipn/g110', 'Gateway\g110\ProcessController@ipn')->name('ipn.g110'); // RozarPay
Route::post('ipn/g111', 'Gateway\g111\ProcessController@ipn')->name('ipn.g111'); // stripeJs
Route::post('ipn/g112', 'Gateway\g112\ProcessController@ipn')->name('ipn.g112'); //instamojo

Route::get('ipn/g501', 'Gateway\g501\ProcessController@ipn')->name('ipn.g501'); // Blockchain
Route::get('ipn/g502', 'Gateway\g502\ProcessController@ipn')->name('ipn.g502'); // Block.io
Route::post('ipn/g503', 'Gateway\g503\ProcessController@ipn')->name('ipn.g503'); // CoinPayment
Route::post('ipn/g504', 'Gateway\g504\ProcessController@ipn')->name('ipn.g504'); // CoinPayment ALL
Route::post('ipn/g505', 'Gateway\g505\ProcessController@ipn')->name('ipn.g505'); // Coingate
Route::post('ipn/g506', 'Gateway\g506\ProcessController@ipn')->name('ipn.g506'); // Coinbase commerce


Route::name('express.')->prefix('express')->group(function () {
    Route::get('/initiate', 'ExpressPaymentController@initiate')->name('initiate');
    Route::get('/initiate/message', 'ExpressPaymentController@initiateError')->name('error');
    Route::get('/payment/{trx}', 'ExpressPaymentController@payment')->name('payment');
    Route::get('/gateway/preview/{encrypt_id}', 'ExpressPaymentController@gatewayPreview')->name('gateway.preview');
    Route::get('/payment-preview', 'ExpressPaymentController@paymentPreview')->name('payment-preview');

    Route::get('/payment-confirm', 'Gateway\PaymentController@depositConfirm')->name('payment.confirm');

    Route::get('/wallet-payment/', 'ExpressPaymentController@walletPayment')->name('wallet.payment');
    Route::post('/wallet-payment/', 'ExpressPaymentController@walletPaymentPost')->name('wallet.payment.confirm');
    Route::get('/done/{trx}', 'ExpressPaymentController@done')->name('done');

    Route::group(['middleware' => 'guest'], function () {
        Route::post('/login', 'ExpressPaymentController@expressSignIn')->name('signin');
    });
});

/*
 * Invoice Payment Controller
 */

Route::group(['prefix' => 'invoice'], function () {
    Route::get('/invoice-download/{token}', 'InvoicePaymentController@getInvoicePdf')->name('getInvoice.pdf');
    Route::any('/makePayment/{token}', 'InvoicePaymentController@getInvoicePayment')->name('getInvoice.payment');
    Route::get('/preview/{gateway_id}', 'InvoicePaymentController@invoicePreviewPayment')->name('invoice.preview');
    Route::get('/initiate/message', 'InvoicePaymentController@initiateError')->name('invoice.initiate.error');

    Route::get('/wallet-confirm/{trx}', 'InvoicePaymentController@invoicePreviewToConfirm')->name('invoice.preview.confirm');

    Route::get('/payment-preview', 'InvoicePaymentController@invoiceDepositPreview')->name('invoice.deposit-preview');
    Route::get('/payment-confirm', 'Gateway\PaymentController@depositConfirm')->name('invoice.deposit.confirm');

    Route::put('/pay/confirm/{invoice_id}', 'InvoicePaymentController@payToWalletConfirm')->name('invoice.confirm.id');

    Route::group(['middleware' => 'guest'], function () {
        Route::post('/login', 'InvoicePaymentController@invoiceSignIn')->name('invoice.signin');
    });
});


/*
 * Home Page
 */
Route::get('/', 'FrontendController@index')->name('home');
Route::get('/change-lang/{lang}', 'FrontendController@changeLang')->name('lang');

Route::name('home.')->group(function () {
    Route::get('/about', 'FrontendController@about')->name('about');
    Route::get('/announce', 'FrontendController@announce')->name('announce');
    Route::get('/announce/details/{id?}/{slug?}', 'FrontendController@announceDetails')->name('announce.details');

    Route::get('/menu/{id}/{slug?}', 'FrontendController@menu')->name('menu');
    Route::get('/faqs', 'FrontendController@faq')->name('faq');
    Route::get('/contact', 'FrontendController@contact')->name('contact');
    Route::post('/contact', 'FrontendController@contactSubmit')->name('contact.send');
    Route::get('/info/{id}/{slug?}', 'FrontendController@policyInfo')->name('policy');
    Route::post('/subscribe', 'FrontendController@subscribe')->name('subscribe');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/merchant-account', 'FrontendController@merchantRegister')->name('merchant-register');
});


Route::name('user.')->prefix('user')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logoutGet')->name('logout');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify-code');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');

    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send_verify_code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify_email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify_sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware('ckstatus')->group(function () {

            Route::get('home', 'UserController@index')->name('home');
            Route::get('transaction', 'UserController@transaction')->name('transaction');
            Route::get('transaction/search', 'UserController@transactionSearch')->name('transaction.search');

            /*
             * Money Transfer
             */
            Route::get('/transfer', 'UserController@moneyTransfer')->name('moneyTransfer');
            Route::post('/transfer', 'UserController@startTransfer')->name('startTransfer');
            Route::get('/transfer/preview/{cryptId}', 'UserController@previewTransfer')->name('previewTransfer');
            Route::post('/transfer/confirm', 'UserController@confirmTransfer')->name('confirm.transfer');
            Route::get('/transfer/received', 'UserController@transferIncoming')->name('transferIncoming');
            Route::get('/transfer/sent', 'UserController@transferOutgoing')->name('transferOutgoing');
            Route::put('/transfer/sent/{id}', 'UserController@transferRelease')->name('transferRelease');

            /*
             * Exchange Money
             */
            Route::get('exchange', 'UserController@exchange')->name('exchange');
            Route::get('/exchange/calculation', 'UserController@exchangeCalculationAvoid');
            Route::post('exchange/calculation', 'UserController@exchangeCalculation')->name('exchange.calculation');
            Route::get('exchange/confirm', 'UserController@exchangeConfirm')->name('exchange.confirm');
            Route::get('exchange-log', 'UserController@exchangeLog')->name('exchangeLog');
            Route::get('exchange-log/search', 'UserController@exchangeLogSearch')->name('exchangeLog.search');

            /*
             * Request Money
             */
            Route::get('request/to-me', 'UserController@requestMoney')->name('request-money.inbox');
            Route::post('request/action', 'UserController@moneyReceivedAction')->name('request-money.action');
            Route::get('request/my-request', 'UserController@requestMoneySendLog')->name('request-money.sent');
            Route::get('request/now', 'UserController@makeRequestMoney')->name('request-money.create');
            Route::post('request/now', 'UserController@requestMoneyStore')->name('request-money.store');

            /*
             * Invoice Manage
             */
            Route::get('invoice', 'UserController@invoice')->name('invoice');
            Route::get('invoice/create', 'UserController@invoiceCreate')->name('invoice.create');
            Route::post('invoice/create', 'UserController@invoiceStore')->name('invoice.store');
            Route::get('invoice/edit/{trx}', 'UserController@invoiceEdit')->name('invoice.edit');
            Route::put('invoice/update/{trx}', 'UserController@invoiceUpdate')->name('invoice.Update');
            Route::put('invoice/cancel/{trx}', 'UserController@invoiceCancel')->name('invoice.cancel');
            Route::get('invoice/sendmail/{invoice_id}', 'UserController@invoiceSendToMail')->name('invoice.sendmail');

            /*
             *  Voucher
             */
            Route::get('vouchers', 'UserController@vouchers')->name('vouchers');
            Route::get('vouchers/create', 'UserController@voucherNewVoucher')->name('vouchers.new_voucher');
            Route::post('vouchers/create', 'UserController@NewVoucherPreview')->name('vouchers.preview');
            Route::get('vouchers/create_voucher', 'UserController@createVoucher')->name('vouchers.create_voucher');
            Route::get('vouchers/redeem', 'UserController@voucherActiveCode')->name('vouchers.active_code');
            Route::post('vouchers/redeem', 'UserController@voucherActiveCodePreview')->name('active_code.preview');
            Route::post('voucher/redeem-preview', 'UserController@voucherSaveCode')->name('voucher.SaveCode');
            Route::get('vouchers/redeem-log', 'UserController@voucherRedeemLog')->name('vouchers.redeemLog');

            /*
             *  User Support Ticket
             */
            Route::get('supportTicket', 'UserController@supportTicket')->name('ticket');
            Route::get('openSupportTicket', 'UserController@openSupportTicket')->name('ticket.open');
            Route::post('openSupportTicket', 'UserController@storeSupportTicket')->name('ticket.store');
            Route::get('supportMessage/{ticket}', 'UserController@supportMessage')->name('message');
            Route::put('storeSupportMessage/{ticket}', 'UserController@supportMessageStore')->name('message.store');
            Route::get('ticketDownload/{ticket}', 'UserController@ticketDownload')->name('ticket.download');
            Route::post('ticketDelete', 'UserController@ticketDelete')->name('ticket.delete');


            Route::get('edit-profile', 'UserController@editProfile')->name('edit-profile');
            Route::post('edit-profile', 'UserController@submitProfile');

            Route::get('doc-ver', 'UserController@document')->name('doc-ver');
            Route::post('doc-ver', 'UserController@docVerify');


            Route::get('change-password', 'UserController@changePassword')->name('change-password');
            Route::post('change-password', 'UserController@submitPassword');

            //      2FA
            Route::get('security/two/step', 'UserController@twoFactorAuth')->name('twoFA');
            Route::post('g2fa-create', 'UserController@create2fa')->name('go2fa.create');
            Route::post('g2fa-disable', 'UserController@disable2fa')->name('disable.2fa');

            Route::get('deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit-insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('deposit-preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('deposit-confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');


            Route::get('/deposit-log', 'UserController@depositLog')->name('depositLog');


            Route::get('manual/deposit-preview', 'Gateway\PaymentController@manualDepositPreview')->name('manualDeposit.preview');
            Route::get('manual/deposit', 'Gateway\PaymentController@manualDepositConfirm')->name('manualDeposit.confirm');
            Route::post('manual/deposit', 'Gateway\PaymentController@manualDepositUpdate')->name('manualDeposit.update');

            Route::get('/withdraw-money', 'UserController@withdrawMoney')->name('withdraw.money');
            Route::post('/withdraw-money', 'UserController@withdrawMoneyRequest')->name('withdraw.moneyReq');
            Route::get('/withdraw-preview', 'UserController@withdrawReqPreview')->name('withdraw.preview');
            Route::post('/withdraw-preview', 'UserController@withdrawReqSubmit')->name('withdraw.submit');
            Route::get('/withdraw-log', 'UserController@withdrawLog')->name('withdrawLog');

            Route::get('/api-key', 'UserController@apiKey')->name('api-key');
            Route::post('/api-key', 'UserController@apiKeyStore')->name('api-key.store');
            Route::delete('/api-key', 'UserController@apiKeyDelete')->name('api-key.delete');

            Route::get('/login-history', 'UserController@loginHistory')->name('loginHistory');
            Route::post('/login-history', 'UserController@logoutOthers')->name('logoutOthers');


        });
    });
});


Route::post('/ipn/checkValidUser', 'UserController@checkValidUser')->name('check.valid.user');


Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@authenticate')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');

        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify-code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.change-link');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware(['admin', 'CheckAdminStatus'])->group(function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard')->middleware('adminAuthorize:1');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

        /*
        * Manage Currency
        */
        Route::middleware(['adminAuthorize:2'])->group(function () {
            Route::get('/currency', 'DashboardController@currency')->name('currency');
            Route::post('/currency', 'DashboardController@updateCurrency')->name('update.currency');
            Route::post('/currency/updateApiKey', 'DashboardController@updateRateApiKey')->name('update.ApiKey');
        });

        /*
         * Manage Staff
         */
        Route::middleware(['adminAuthorize:3'])->group(function () {
            Route::get('/staff', 'DashboardController@staff')->name('staff');
            Route::post('/staff', 'DashboardController@storeStaff')->name('storeStaff');
            Route::put('/staff/{id}', 'DashboardController@updateStaff')->name('updateStaff');
        });

        // Users Manager
        Route::middleware(['adminAuthorize:4'])->group(function () {
            Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
            Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
            Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
            Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.emailUnverified');
            Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.smsUnverified');
            Route::get('users/document-request', 'ManageUsersController@documentRequest')->name('users.documentRequest');
            Route::put('users/document-approve/{user}', 'ManageUsersController@documentApprove')->name('users.documentApprove');

            Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
            Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
            Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');

            Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.addSubBalance');
            Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
            Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.all');
            Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
            Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
            Route::get('user/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');
            Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
            Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
            Route::get('user/moneyTransfers/{id}', 'ManageUsersController@moneyTransfer')->name('users.moneyTransfer');
            Route::get('user/moneyExchange/{id}', 'ManageUsersController@moneyExchange')->name('users.moneyExchange');
            Route::get('user/moneyRequest/{id}', 'ManageUsersController@moneyRequest')->name('users.moneyRequest');
            Route::get('user/voucherLog/{id}', 'ManageUsersController@voucherLog')->name('users.voucherLog');
            Route::get('user/invoiceLog/{id}', 'ManageUsersController@invoiceLog')->name('users.invoiceLog');

            // Login History
            Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');
            Route::get('users/login/history', 'ManageUsersController@loginHistory')->name('users.login.history');
        });


        Route::middleware(['adminAuthorize:5'])->group(function () {

            // WITHDRAW SYSTEM
            Route::get('withdraw/log', 'WithdrawalController@log')->name('withdraw.log');
            Route::get('withdraw/{scope}/search', 'WithdrawalController@search')->name('withdraw.search');
            Route::get('withdraw/pending', 'WithdrawalController@pending')->name('withdraw.pending');
            Route::get('withdraw/approved', 'WithdrawalController@approved')->name('withdraw.approved');
            Route::get('withdraw/rejected', 'WithdrawalController@rejected')->name('withdraw.rejected');
            Route::post('withdraw/approve', 'WithdrawalController@approve')->name('withdraw.approve');
            Route::post('withdraw/reject', 'WithdrawalController@reject')->name('withdraw.reject');

            // Withdraw Method
            Route::get('withdraw/method/', 'WithdrawMethodController@methods')->name('withdraw.method.methods');
            Route::get('withdraw/method/new', 'WithdrawMethodController@create')->name('withdraw.method.create');
            Route::post('withdraw/method/store', 'WithdrawMethodController@store')->name('withdraw.method.store');
            Route::get('withdraw/method/edit/{id}', 'WithdrawMethodController@edit')->name('withdraw.method.edit');
            Route::post('withdraw/method/edit/{id}', 'WithdrawMethodController@update')->name('withdraw.method.update');
            Route::post('withdraw/method/activate', 'WithdrawMethodController@activate')->name('withdraw.method.activate');
            Route::post('withdraw/method/deactivate', 'WithdrawMethodController@deactivate')->name('withdraw.method.deactivate');
        });

        Route::middleware(['adminAuthorize:6'])->group(function () {

            // DEPOSIT SYSTEM
            Route::get('deposit', 'DepositController@deposit')->name('deposit.list');
            Route::get('deposit/pending', 'DepositController@pending')->name('deposit.pending');
            Route::get('deposit/rejected', 'DepositController@rejected')->name('deposit.rejected');
            Route::get('deposit/approved', 'DepositController@approved')->name('deposit.approved');
            Route::post('deposit/reject', 'DepositController@reject')->name('deposit.reject');
            Route::post('deposit/approve', 'DepositController@approve')->name('deposit.approve');
            Route::get('deposit/{scope}/search', 'DepositController@search')->name('deposit.search');

            // Manual Methods
            Route::get('deposit/manual-methods', 'ManualGatewayController@index')->name('deposit.manual.index');
            Route::get('deposit/manual-methods/new', 'ManualGatewayController@create')->name('deposit.manual.create');
            Route::post('deposit/manual-methods/new', 'ManualGatewayController@store')->name('deposit.manual.store');
            Route::get('deposit/manual-methods/edit/{id}', 'ManualGatewayController@edit')->name('deposit.manual.edit');
            Route::post('deposit/manual-methods/update/{id}', 'ManualGatewayController@update')->name('deposit.manual.update');
            Route::post('deposit/manual-methods/activate', 'ManualGatewayController@activate')->name('deposit.manual.activate');
            Route::post('deposit/manual-methods/deactivate', 'ManualGatewayController@deactivate')->name('deposit.manual.deactivate');

            // Deposit Gateway
            Route::get('deposit/gateway', 'GatewayController@index')->name('deposit.gateway.index');
            Route::get('deposit/gateway/edit/{code}', 'GatewayController@edit')->name('deposit.gateway.edit');
            Route::post('deposit/gateway/update/{code}', 'GatewayController@update')->name('deposit.gateway.update');
            Route::post('deposit/gateway/remove/{code}', 'GatewayController@remove')->name('deposit.gateway.remove');
            Route::post('deposit/gateway/activate', 'GatewayController@activate')->name('deposit.gateway.activate');
            Route::post('deposit/gateway/deactivate', 'GatewayController@deactivate')->name('deposit.gateway.deactivate');
        });

        // Subscriber
        Route::middleware(['adminAuthorize:7'])->group(function () {
            Route::get('subscriber', 'SubscriberController@index')->name('subscriber.index');
            Route::get('subscriber/send-email', 'SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
            Route::post('subscriber/remove', 'SubscriberController@remove')->name('subscriber.remove');
            Route::post('subscriber/send-email', 'SubscriberController@sendEmail')->name('subscriber.sendEmail');
        });

        // Report
        Route::middleware(['adminAuthorize:8'])->group(function () {

            Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
            Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');

            Route::get('report/money-transfer', 'ReportController@moneyTransfer')->name('report.money-transfer');
            Route::get('report/money-transfer/search', 'ReportController@moneyTransferSearch')->name('report.money-transfer.search');

            Route::get('report/money-exchange', 'ReportController@moneyExchange')->name('report.money-exchange');
            Route::get('report/money-exchange/search', 'ReportController@moneyExchangeSearch')->name('report.money-exchange.search');

            Route::get('report/money-request', 'ReportController@moneyRequest')->name('report.money-request');
            Route::get('report/money-request/search', 'ReportController@moneyRequestSearch')->name('report.money-request.search');

            Route::get('report/voucher-log', 'ReportController@voucherGift')->name('report.voucher-log');
            Route::get('report/voucher-log/search', 'ReportController@voucherGiftSearch')->name('report.voucher-log.search');

            Route::get('report/invoice-log', 'ReportController@invoice')->name('report.invoice-log');
            Route::get('report/invoice-log/search', 'ReportController@invoiceSearch')->name('report.invoice-log.search');
        });


        Route::middleware(['adminAuthorize:9'])->group(function () {
            // Admin Support
            Route::get('tickets-list', 'DashboardController@supportTicket')->name('ticket');
            Route::get('tickets-reply/{id}', 'DashboardController@ticketReply')->name('ticket.reply');
            Route::get('tickets-open', 'DashboardController@openSupportTicket')->name('open.ticket');
            Route::get('tickets-pending', 'DashboardController@pendingSupportTicket')->name('pending.ticket');
            Route::get('tickets-closed', 'DashboardController@closedSupportTicket')->name('closed.ticket');

            Route::put('ticketReplySend/{id}', 'DashboardController@ticketReplySend')->name('ticket.send');
            Route::get('ticketDownload/{ticket}', 'DashboardController@ticketDownload')->name('ticket.download');
            Route::post('ticketDelete', 'DashboardController@ticketDelete')->name('ticket.delete');

            // Contact Topic
            Route::get('contact-topic', 'ContactTopicController@index')->name('contact-topic');
            Route::get('contact-topic/data', 'ContactTopicController@getTopic')->name('get-topic');
            Route::post('contact-topic/store', 'ContactTopicController@storeTopic')->name('store.contact-topic');
            Route::post('contact-topic/update', 'ContactTopicController@updateTopic')->name('update.contact-topic');
            Route::post('contact-topic/delete', 'ContactTopicController@destroyTopic')->name('delete.contact-topic');

        });

        Route::middleware(['adminAuthorize:10'])->group(function () {
            // Plugin
            Route::get('plugin', 'PluginController@index')->name('plugin.index');
            Route::post('plugin/update/{id}', 'PluginController@update')->name('plugin.update');
            Route::post('plugin/activate', 'PluginController@activate')->name('plugin.activate');
            Route::post('plugin/deactivate', 'PluginController@deactivate')->name('plugin.deactivate');
        });

        Route::middleware(['adminAuthorize:11'])->group(function () {
            // Frontend
            Route::name('frontend.')->prefix('frontend')->group(function () {
                Route::post('store', 'FrontendController@store')->name('store');
                Route::post('remove', 'FrontendController@remove')->name('remove');
                Route::post('{id}/update', 'FrontendController@update')->name('update');

                // FAQ
                Route::get('faq', 'FrontendController@faqIndex')->name('faq.index');
                Route::get('faq/{id}/{slug}/edit', 'FrontendController@faqEdit')->name('faq.edit');
                Route::get('faq/new', 'FrontendController@faqNew')->name('faq.new');

                // Blog
                Route::get('announce/', 'FrontendController@announceIndex')->name('blog.index');
                Route::get('announce/edit/{id}/{slug}', 'FrontendController@announceEdit')->name('blog.edit');
                Route::get('announce/new', 'FrontendController@announceNew')->name('blog.new');

                // Our Team
                Route::get('team/', 'FrontendController@teamIndex')->name('team.index');
                Route::get('team/edit/{id}/{slug}', 'FrontendController@teamEdit')->name('team.edit');
                Route::get('team/new', 'FrontendController@teamNew')->name('team.new');



                // Company policy
                Route::get('company_policy/', 'FrontendController@companyPolicy')->name('companyPolicy.index');
                Route::get('company_policy/{id}/{slug}/edit', 'FrontendController@companyPolicyEdit')->name('companyPolicy.edit');
                Route::get('company_policy/new', 'FrontendController@companyPolicyNew')->name('companyPolicy.new');

                // Manage Menu
                Route::get('menu/', 'FrontendController@menu')->name('menu.index');
                Route::get('menu/{id}/{slug}/edit', 'FrontendController@menuEdit')->name('menu.edit');
                Route::get('menu/new', 'FrontendController@menuNew')->name('menu.new');

                Route::get('contact', 'FrontendController@sectionContact')->name('section.contact.edit');
                Route::get('callToAction', 'FrontendController@sectionTransaction')->name('callToAction');
                Route::get('homeContent', 'FrontendController@homeContent')->name('homeContent');

                Route::get('about', 'FrontendController@sectionAbout')->name('about.edit');
                Route::post('about/{id}/update', 'FrontendController@sectionAboutUpdate')->name('about.update');
                Route::get('developer', 'FrontendController@sectionDeveloper')->name('developer.edit');
                Route::post('developer/{id}/update', 'FrontendController@sectionDeveloperUpdate')->name('developer.update');

                // SEO
                Route::get('seo', 'FrontendController@seoEdit')->name('seo.edit');

                // Social
                Route::get('social', 'FrontendController@socialIndex')->name('social.index');

                // Testimonial
                Route::get('testimonial', 'FrontendController@testimonialIndex')->name('testimonial.index');
                Route::get('testimonial/new', 'FrontendController@testimonialNew')->name('testimonial.new');
                Route::get('testimonial/edit/{id}', 'FrontendController@testimonialEdit')->name('testimonial.edit');


                // why choose us
                Route::get('services', 'FrontendController@whychooseIndex')->name('whychoose.index');
                Route::get('services/new', 'FrontendController@whychooseNew')->name('whychoose.new');
                Route::get('services/edit/{id}', 'FrontendController@whychooseEdit')->name('whychoose.edit');

                // Flow Step
                Route::get('process', 'FrontendController@flowstepIndex')->name('flowstep.index');
                Route::get('process/new', 'FrontendController@flowstepNew')->name('flowstep.new');
                Route::get('process/edit/{id}', 'FrontendController@flowstepEdit')->name('flowstep.edit');


                // how it work
                Route::get('whychooseus', 'FrontendController@howitworkIndex')->name('howitwork.index');
                Route::get('whychooseus/new', 'FrontendController@howitworkNew')->name('howitwork.new');
                Route::get('whychooseus/edit/{id}', 'FrontendController@howitworkEdit')->name('howitwork.edit');

            });
        });

        Route::middleware(['adminAuthorize:12'])->group(function () {

            // General Setting
            Route::get('setting', 'GeneralSettingController@index')->name('setting.index');
            Route::post('setting', 'GeneralSettingController@update')->name('setting.update');

            // transaction Fee
            Route::get('transaction-fees', 'GeneralSettingController@transactionFees')->name('transaction-fees.index');
            Route::post('transaction-fees', 'GeneralSettingController@transactionFeesUpdate')->name('transaction-fees.update');

            // Language Manager
            Route::get('setting/language/manager', 'LanguageController@langManage')->name('setting.language-manage');
            Route::post('setting/language/manager', 'LanguageController@langStore')->name('setting.language-manage-store');
            Route::delete('setting/language-manage/{id}', 'LanguageController@langDel')->name('setting.language-manage-del');
            Route::get('setting/language-key/{id}', 'LanguageController@langEdit')->name('setting.language-key');
            Route::put('setting/key-update/{id}', 'LanguageController@langUpdate')->name('setting.key-update');
            Route::post('setting/language-manage-update/{id}', 'LanguageController@langUpdatepp')->name('setting.language-manage-update');
            Route::post('setting/language-import', 'LanguageController@langImport')->name('setting.import_lang');

            // Logo-Icon
            Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo-icon');
            Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo-icon');

        });

        // Email Setting
        Route::middleware(['adminAuthorize:13'])->group(function () {
            Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email-template.global');
            Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email-template.global');
            Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email-template.setting');
            Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email-template.setting');
            Route::get('email-template/index', 'EmailTemplateController@index')->name('email-template.index');
            Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email-template.edit');
            Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email-template.update');
            Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email-template.sendTestMail');
        });

        // SMS Setting
        Route::middleware(['adminAuthorize:14'])->group(function () {
            Route::get('sms-template/global', 'SmsTemplateController@smsSetting')->name('sms-template.global');
            Route::post('sms-template/global', 'SmsTemplateController@smsSettingUpdate')->name('sms-template.global');
            Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms-template.index');
            Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms-template.edit');
            Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms-template.update');
            Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('email-template.sendTestSMS');
        });




    });
});
