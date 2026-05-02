<?php

namespace App\Providers;

use App\Models\PengajuanSewa;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
                Paginator::useBootstrapFive();

        if (! $this->app->runningInConsole()) {
            PengajuanSewa::syncExpiredRentals();
        }
    }
}
