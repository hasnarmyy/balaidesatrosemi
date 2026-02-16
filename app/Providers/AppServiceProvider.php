<?php

namespace App\Providers;

use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Observers\PegawaiObserver;
use App\Observers\JabatanObserver;
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
    public function boot()
    {
        // Pegawai
        \App\Models\Pegawai::observe(\App\Observers\PegawaiObserver::class);
        \App\Models\Pegawai::observe(\App\Observers\PegawaiPayrollDetailObserver::class);

        // Jabatan
        \App\Models\Jabatan::observe(\App\Observers\JabatanObserver::class);
        \App\Models\Jabatan::observe(\App\Observers\JabatanPayrollDetailObserver::class);
    }
}
