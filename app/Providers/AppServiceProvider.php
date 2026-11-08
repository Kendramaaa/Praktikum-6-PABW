<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;

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
        // Tampilan pagination mengikuti Bootstrap 5 (sama seperti layout aplikasi)
        Paginator::useBootstrapFive();

        // Tanggal & waktu ditampilkan dalam bahasa Indonesia
        Carbon::setLocale('id');
    }
}
