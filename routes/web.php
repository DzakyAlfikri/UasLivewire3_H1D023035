<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\PegawaiManager;
use App\Livewire\JabatanManager;
use App\Livewire\UnitKerjaManager;
use App\Livewire\CutiManager;
use App\Livewire\LaporanAbsensi;
use App\Livewire\AbsensiManager;
use App\Livewire\PegawaiDashboard; // Add this import
use App\Livewire\CutiRequest; // Add this import

// Home route with redirection
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Custom login routes
Route::get('login', 'App\Http\Controllers\Auth\CustomLoginController@showLoginForm')->name('login');
Route::post('login', 'App\Http\Controllers\Auth\CustomLoginController@login');
Route::post('logout', 'App\Http\Controllers\Auth\CustomLoginController@logout')->name('logout');

// Dashboard routes
Route::get('/dashboard', function () {
    // Only allow admins to access admin dashboard
    if (Auth::user() && Auth::user()->role === 'pegawai') {
        return redirect()->route('pegawai.dashboard');
    }
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Employee dashboard - UPDATED to use Livewire
Route::get('/pegdashboard', PegawaiDashboard::class)->middleware('auth')->name('pegawai.dashboard');

// Redirect /home to the right dashboard
Route::get('/home', function () {
    if (Auth::user() && Auth::user()->role === 'pegawai') {
        return redirect()->route('pegawai.dashboard');
    }
    return redirect()->route('dashboard');
})->middleware('auth');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::get('/pegawai', PegawaiManager::class)->name('pegawai');
    Route::get('/jabatan', JabatanManager::class)->name('jabatan');
    Route::get('/unit-kerja', UnitKerjaManager::class)->name('unit-kerja');
    Route::get('/cuti', CutiManager::class)->name('cuti');
    Route::get('/laporan-absensi', LaporanAbsensi::class)->name('laporan-absensi');
    Route::get('/absensi', AbsensiManager::class)->name('absensi');
    // Add this route
    Route::get('/absensi/pegawai', App\Livewire\AbsensiPegawai::class)
        ->middleware('auth')
        ->name('absensi.pegawai');
    // Add this route
    Route::get('/gaji/pegawai', App\Livewire\GajiPegawai::class)
        ->middleware('auth')
        ->name('gaji.pegawai');
});

// Add this route for leave request functionality
Route::get('/cuti/create', CutiRequest::class)->middleware('auth')->name('cuti.create');