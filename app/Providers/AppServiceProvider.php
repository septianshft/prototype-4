<?php

namespace App\Providers;

use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\View;
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
        View::composer('partials.sidebar', function ($view) {
        $jumlahPendingLaporan = Laporan_Beasiswa::where('status_acc', 'pending')->count();
        $view->with('jumlahPendingLaporan', $jumlahPendingLaporan);
    });
    }
}
