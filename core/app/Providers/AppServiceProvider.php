<?php

namespace App\Providers;

use App\Frontend;
use App\GeneralSetting;
use App\Language;
use App\Plugin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $viewShare['general'] = GeneralSetting::first();
        $viewShare['socials'] = Frontend::where('key','social.item')->get();
        $viewShare['shortAbout'] = Frontend::where('key','homecontent')->first();
        $viewShare['menus'] = Frontend::where('key','menu')->get();
        $viewShare['company_policy'] = Frontend::where('key','company_policy')->get();


        $viewShare['plugins'] = Plugin::all();
        $viewShare['lan'] = Language::all();
        $viewShare['plugins'] = Plugin::all();
        view()->share($viewShare);

        view()->composer('partials.seo', function ($view) {
            $seo = \App\Frontend::where('key', 'seo')->first();
            $view->with([
                'seo' => $seo ? $seo->value : $seo,
            ]);
        });

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'           => \App\User::banned()->count(),
                'email_unverified_users_count' => \App\User::emailUnverified()->count(),
                'sms_unverified_users_count'   => \App\User::smsUnverified()->count(),
                'pending_withdrawals_count'    => \App\Withdrawal::pending()->count(),
            ]);
        });
    }
}
