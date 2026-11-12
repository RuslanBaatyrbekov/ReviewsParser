<?php

namespace App\Providers;

use App\Contracts\ReviewSource;
use App\Services\YandexParser;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReviewSource::class, fn () => new YandexParser(
            delaySeconds: (int) config('services.yandex.mock_delay', 2),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
