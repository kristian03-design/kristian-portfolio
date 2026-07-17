<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

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
        if (app()->environment('production') || env('VERCEL')) {
            URL::forceScheme('https');
        }

        // Configure upload rate limiter: 10 uploads per minute
        RateLimiter::for('uploads', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip() ?: 'guest';
            return Limit::perMinute(10)->by($key);
        });
    }
}
