<?php

namespace App\Providers;

use App\Models\Pegawai;
use App\Observers\PegawaiObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\PegawaiManager;

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
        Livewire::component('pegawai-manager', PegawaiManager::class);
        Pegawai::observe(PegawaiObserver::class);
    }
}
