<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
   Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');
});


    // DASHBOARD BY ROLE
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    Route::middleware('role:pemilik')->group(function () {
        Route::get('/pemilik/dashboard', function () {
            return view('pemilik.dashboard');
        })->name('pemilik.dashboard');
    });

    Route::middleware('role:penyewa')->group(function () {
        Route::get('/penyewa/dashboard', function () {
            return view('penyewa.dashboard');
        })->name('penyewa.dashboard');
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
