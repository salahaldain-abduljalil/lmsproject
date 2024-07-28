<?php

namespace App\Providers;
use App\Models\smtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

     if (Schema::hasTable('smtp_settings')) {

        $smtpsetting = smtpSetting::first();

        if ($smtpsetting) {
        $data = [
         'driver' => $smtpsetting->mailer,
         'host' => $smtpsetting->host,
         'port' => $smtpsetting->port,
         'username' => $smtpsetting->username,
         'password' => $smtpsetting->password,
         'encryption' => $smtpsetting->encryption,
         'from' => [
             'address' => $smtpsetting->from_address,
             'name' => 'Easycourselms'
         ]

         ];
         Config::set('mail',$data);
        }

     }
    }
}
