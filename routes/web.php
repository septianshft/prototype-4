<?php

use App\Http\Controllers\dashboard as ControllersDashboard;
use App\Livewire\Admin\UserManager;
use App\Livewire\Beasiswa\ApplyBeasiswaPage;
use App\Livewire\Beasiswa\HasilSeleksiBeasiswa;
use App\Livewire\Beasiswa\LaporanBeasiswaPage;
use App\Livewire\Beasiswa\LaporanBeasiswaShow;
use App\Livewire\Beasiswa\ManajemenBeasiswa;
use App\Livewire\Beasiswa\Partials\ApplyBeasiswaPage as PartialsApplyBeasiswaPage;
use App\Livewire\Beasiswa\partials\LaporanBeasiswaShow as PartialsLaporanBeasiswaShow;
use App\Livewire\Beasiswa\SeleksiBeasiswa;
use App\Livewire\Dashboard\Mahasiswadashboard;
use App\Livewire\Dashboard\Roledashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Mahasiswa\ManajemenMahasiswa;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/login', function () {
    return view('login');
})->name('home');

// Route::view('/dashboard', 'dashboard')
//     ->middleware(['auth', 'verified', 'role:mahasiswa']) // Add role:user middleware
//     ->name('dashboard');

// Talent Dashboard R

// Route::view('dashboard', 'dashboard') // Assuming you will create a talent.dashboard view
//     ->middleware(['auth', 'verified', 'role:vicedirector'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


//Route Beasiswa
Route::get('/beasiswa', ManajemenBeasiswa::class)->name('beasiswa');
Route::get('/hasil_seleksi_beasiswa', HasilSeleksiBeasiswa::class)->name('hasil_seleksi_beasiswa');

require __DIR__ . '/auth.php';

# Admin Routes
Route::view('dashboard', 'dashboard') // Assuming you have an admin.dashboard view
    ->name('dashboard');

Route::get('livewire/admin/user-manager', UserManager::class) //user manager
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.user-manager');

Route::get('/mahasiswa', ManajemenMahasiswa::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('mahasiswa.manajemen');


#dosen Routes
// Route::get('/laporan_beasiswa', LaporanBeasiswa::class)->name('laporan_beasiswa');

Route::get('/laporan-beasiswa', LaporanBeasiswaPage::class)
    ->middleware('auth')
    ->name('laporan_beasiswa');

Route::get('/laporan-beasiswa/{id}', PartialsLaporanBeasiswaShow::class)
    ->middleware(['auth'])
    ->name('laporan.beasiswa.show');

Route::get('/seleksi-beasiswa', SeleksiBeasiswa::class)
    ->middleware(['auth'])
    ->name('seleksi.beasiswa');

Route::get('/mahasiswadashboard', Mahasiswadashboard::class)
    ->middleware(['auth', 'verified', 'role:mahasiswa'])
    ->name('dashboard.mahasiswa');

Route::get('/roledashboard', Roledashboard::class)
    ->middleware(['auth'])
    ->name('role.dashboard');
