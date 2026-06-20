<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    // public const HOME = '/home';
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // AntiTheft API rate limiting (authenticated endpoints)
        RateLimiter::for('antitheft', function (Request $request) {
            // Use API key for rate limiting if available
            $apiKey = $request->attributes->get('api_key');
            $identifier = $apiKey ? hash('sha256', $apiKey) : $request->ip();
            
            return [
                // 1000 requests per hour for authenticated API users
                Limit::perHour(1000)->by('antitheft:hour:' . $identifier),
                // 200 requests per minute for burst protection
                Limit::perMinute(200)->by('antitheft:minute:' . $identifier),
            ];
        });

        // Public AntiTheft API rate limiting (stricter)
        RateLimiter::for('public-antitheft', function (Request $request) {
            return [
                // 100 requests per hour for public endpoints
                Limit::perHour(100)->by('public-antitheft:hour:' . $request->ip()),
                // 10 requests per minute for public endpoints
                Limit::perMinute(10)->by('public-antitheft:minute:' . $request->ip()),
            ];
        });

        // Bulk operations rate limiting (even stricter)
        RateLimiter::for('bulk-operations', function (Request $request) {
            $apiKey = $request->attributes->get('api_key');
            $identifier = $apiKey ? hash('sha256', $apiKey) : $request->ip();
            
            return [
                // 50 bulk operations per hour
                Limit::perHour(50)->by('bulk:hour:' . $identifier),
                // 5 bulk operations per minute
                Limit::perMinute(5)->by('bulk:minute:' . $identifier),
            ];
        });
    }
}
