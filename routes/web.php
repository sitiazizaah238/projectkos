<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pemilik\ProfilePemilikController;
use App\Http\Controllers\Pemilik\PemilikKosController;
use App\Http\Controllers\Pemilik\PemilikKamarController;

Route::get('/', function () {
    return view('welcome');
});

// GLOBAL DASHBOARD (redirect by role)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'pemilik') {
        return redirect()->route('pemilik.dashboard');
    } else {
        return redirect()->route('penyewa.dashboard');
    }
})->middleware('auth')->name('dashboard');

// SEMUA HARUS LOGIN
Route::middleware('auth')->group(function () {

    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::get('/admin/profile', function () {
            return view('admin.profile');
        })->name('admin.profile');
        // DATA PEMILIK
        Route::get('/admin/pemilik', [App\Http\Controllers\Admin\PemilikController::class, 'index'])
            ->name('admin.pemilik.index');

        Route::get('/admin/pemilik/{id}', [App\Http\Controllers\Admin\PemilikController::class, 'show'])
            ->name('admin.pemilik.show');

        Route::get('/admin/pemilik/{id}/edit', [App\Http\Controllers\Admin\PemilikController::class, 'edit'])
            ->name('admin.pemilik.edit');

        Route::put('/admin/pemilik/{id}', [App\Http\Controllers\Admin\PemilikController::class, 'update'])
            ->name('admin.pemilik.update');

        Route::delete('/admin/pemilik/{id}', [App\Http\Controllers\Admin\PemilikController::class, 'destroy'])
            ->name('admin.pemilik.destroy');
        // ================= DATA KOS (ADMIN) =================
        Route::get('/admin/kos', [App\Http\Controllers\Admin\KosController::class, 'index'])
            ->name('admin.kos.index');

        Route::post('/admin/kos/{id}/approve', [App\Http\Controllers\Admin\KosController::class, 'approve'])
            ->name('admin.kos.approve');

        Route::post('/admin/kos/{id}/reject', [App\Http\Controllers\Admin\KosController::class, 'reject'])
            ->name('admin.kos.reject');
        // ================= LOG AKTIVITAS =================
        Route::get(
            '/admin/log-aktivitas',
            [App\Http\Controllers\Admin\LogAktivitasController::class, 'index']
        )->name('admin.log.index');
    });



    // DASHBOARD BY ROLE
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // DASHBOARD + PROFILE PEMILIK
    Route::middleware('role:pemilik')->group(function () {

        Route::get('/pemilik/dashboard', function () {
            return view('pemilik.dashboard');
        })->name('pemilik.dashboard');
        Route::resource('/pemilik/kos', PemilikKosController::class)
            ->names('pemilik.kos');


        // PROFILE PEMILIK
        Route::get('/pemilik/profile', [ProfilePemilikController::class, 'index'])
            ->name('pemilik.profile');

        Route::post('/pemilik/profile', [ProfilePemilikController::class, 'update'])
            ->name('pemilik.profile.update');
        Route::resource('/pemilik/kamar', PemilikKamarController::class)
            ->names('pemilik.kamar');
    });


    Route::middleware('role:penyewa')->group(function () {
        Route::get('/penyewa/dashboard', function () {
            return view('penyewa.dashboard');
        })->name('penyewa.dashboard');
        Route::get(
            '/penyewa/kos/{id}',
            [App\Http\Controllers\Penyewa\KosController::class, 'show']
        )->name('penyewa.kos.detail');
        Route::get(
            '/penyewa/cari-kos',
            [App\Http\Controllers\Penyewa\KosController::class, 'index']
        )->name('penyewa.cari.kos');
        // PROFILE PENYEWA
Route::get('/penyewa/profile', [App\Http\Controllers\Penyewa\ProfilePenyewaController::class, 'index'])
    ->name('penyewa.profile');

Route::post('/penyewa/profile', [App\Http\Controllers\Penyewa\ProfilePenyewaController::class, 'update'])
    ->name('penyewa.profile.update');
    });
});

// register khusus
Route::get('/register-pemilik', [AuthController::class, 'regPemilik'])
    ->name('register.pemilik');
Route::post('/register-pemilik', [AuthController::class, 'storePemilik']);

Route::get('/register-penyewa', [AuthController::class, 'regPenyewa'])
    ->name('register.penyewa');
Route::post('/register-penyewa', [AuthController::class, 'storePenyewa']);

require __DIR__ . '/auth.php';
