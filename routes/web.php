<?php

use App\Livewire\Admin\UserManager;
use App\Livewire\Beasiswa\HasilSeleksiBeasiswa;
use App\Livewire\Beasiswa\LaporanBeasiswa;
use App\Livewire\Beasiswa\ManajemenBeasiswa;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Mahasiswa\ManajemenMahasiswa;

Route::get('/login', function () {
    return view('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'role:mahasiswa']) // Add role:user middleware
    ->name('dashboard');

// Talent Dashboard Route
Route::view('talent/dashboard', 'talent.dashboard') // Assuming you will create a talent.dashboard view
    ->middleware(['auth', 'verified', 'role:talent'])
    ->name('talent.dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


//Route Beasiswa
Route::get('/beasiswa', ManajemenBeasiswa::class)->name('beasiswa');
Route::get('/laporan_beasiswa', LaporanBeasiswa::class)->name('laporan_beasiswa');
Route::get('/hasil_seleksi_beasiswa', HasilSeleksiBeasiswa::class)->name('hasil_seleksi_beasiswa');

require __DIR__ . '/auth.php';

//# Admin Routes
Route::view('admin/dashboard', 'admin.dashboard') // Assuming you have an admin.dashboard view
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::get('livewire/admin/user-manager', UserManager::class) //user manager
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.user-manager');

Route::get('/mahasiswa', ManajemenMahasiswa::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('mahasiswa.manajemen');
