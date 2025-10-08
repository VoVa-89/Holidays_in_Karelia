<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

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
        // В локальной среде (Open Server) принудительно используем mailer=log,
        // чтобы избежать ошибок подключения к SMTP (mailpit и т.п.)
        if (App::environment('local')) {
            Config::set('mail.default', 'log');
        }
    }
}
