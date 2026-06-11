<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* 
            // A. Bind Interface ke Concrete Class
            $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
            // B. Mendaftarkan Singleton (Objek yang dibuat hanya sekali selama request)
            $this->app->singleton(PaymentGateway::class, function ($app) {
                return new PaymentGateway(config('services.xendit.api_key'));
            });
            // C. Menggabungkan (Merge) File Config Custom
            $this->mergeConfigFrom(
                __DIR__.'/../config/custom_settings.php', 'custom_settings'
            );
            // D. Bind Instance spesifik
            $this->app->instance('api_token', 'secret-token-key-123');
        */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* 
            // A. Mengatur panjang default string database (Solusi error key/index migration)
            Schema::defaultStringLength(191);
            // B. Share data ke seluruh View Blade otomatis
            View::share('siteName', 'CyberLoka Portal');
            // C. Mendaftarkan Database Model Observer (Event Model)
            User::observe(UserObserver::class);
            // D. Membuat Validation Rule Kustom sendiri
            Validator::extend('no_spaces', function ($attribute, $value, $parameters, $validator) {
                return !preg_match('/\s/', $value);
            });
            // E. Mendefinisikan Authorization Gate (Hak Akses)
            Gate::define('access-admin', function ($user) {
                return $user->role === 'admin';
            });
        */

        Carbon::setLocale('id');
        // Atau set time zone jika diperlukan
        date_default_timezone_set('Asia/Jakarta');
    }
}
