<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// bawaan breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// SEMUA HARUS LOGIN
Route::middleware('auth')->group(function () {

    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // dashboard by role
    Route::middleware('role:admin')
        ->get('/admin/dashboard', fn()=>view('admin.dashboard'));

    Route::middleware('role:pemilik')
        ->get('/pemilik/dashboard', fn()=>view('pemilik.dashboard'));

    Route::middleware('role:penyewa')
        ->get('/penyewa/dashboard', fn()=>view('penyewa.dashboard'));
});
Route::get('/register-pemilik',[AuthController::class,'regPemilik'])
    ->name('register.pemilik');

Route::post('/register-pemilik',[AuthController::class,'storePemilik']);

Route::get('/register-penyewa',[AuthController::class,'regPenyewa'])
    ->name('register.penyewa');

Route::post('/register-penyewa',[AuthController::class,'storePenyewa']);


require __DIR__.'/auth.php';
