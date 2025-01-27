<?php

namespace App\Providers;

use App\Models\TeamSettings;
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
        if (Schema::hasTable('team_settings')) {
            foreach (TeamSettings::all() as $setting) {
                Config::set('mail.mailers.custom_mailer_'.$setting->team_id, [
                    'transport' => 'smtp',
                    'url' => config('mail.mailers.smtp.url'),
                    'host' => $setting->smtp_host,
                    'port' => $setting->smtp_port,
                    'encryption' => $setting->smtp_encryption,
                    'username' => $setting->smtp_username,
                    'password' => $setting->smtp_password,
                    'timeout' => null,
                    'local_domain' => config('mail.mailers.smtp.local_domain'),
                ]);
            }
        }
    }
}
