<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        // Override default 'api' rate limiter untuk membatasi 5 request per menit
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(2)
        //         ->by($request->ip())
        //         // ->response(fn() => response()->json([
        //         //     'status'  => false,
        //         //     'message' => 'Terlalu banyak permintaan. Batas 5 request per menit terlampaui.',
        //         //     'data'    => null,
        //         // ], 429))
        //     ;
        // });

        RateLimiter::for('api', function (Request $request) {
            $retryMinutes = 5;
            $retrySeconds = $retryMinutes * 60;
            // Parameter pertama: jumlah menit (5 = 300 detik)
            // Parameter kedua: max attempts (2)
            return Limit::perMinutes($retryMinutes, 20)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) use ($retrySeconds) {
                    return response()->json([
                        'status'  => false,
                        'message' => __('auth.throttle', ['seconds' => $headers['Retry-After'] ?? $retrySeconds]),
                        'data'    => null,
                    ], 429, $headers);
                });
        });
    }
}
