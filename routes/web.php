<?php

use App\Http\Controllers\Admin\KosController as AdminKosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pemilik\ProfilePemilikController;
use App\Http\Controllers\Pemilik\PemilikKosController;
use App\Http\Controllers\Pemilik\PemilikKamarController;
use App\Http\Controllers\Penyewa\ProfilePenyewaController;
use App\Http\Controllers\Penyewa\RecommendationController;
use App\Http\Controllers\Penyewa\KosController as PenyewaKosController;
use App\Http\Controllers\Penyewa\PembayaranController;

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
        Route::get(
            '/pemilik/pengajuan',
            [App\Http\Controllers\Pemilik\PengajuanController::class, 'index']
        )->name('pemilik.pengajuan.index');
        Route::get(
            '/pemilik/pengajuan/{id}',
            [App\Http\Controllers\Pemilik\PengajuanController::class, 'show']
        )->name('pemilik.pengajuan.show');

        Route::post(
            '/pemilik/pengajuan/{id}/approve',
            [App\Http\Controllers\Pemilik\PengajuanController::class, 'approve']
        )->name('pemilik.pengajuan.approve');

        Route::post(
            '/pemilik/pengajuan/{id}/reject',
            [App\Http\Controllers\Pemilik\PengajuanController::class, 'reject']
        )->name('pemilik.pengajuan.reject');
        Route::get(
            '/pemilik/pembayaran',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'index']
        )->name('pemilik.pembayaran.index');

        Route::get(
            '/pemilik/pembayaran/create',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'create']
        )->name('pemilik.pembayaran.create');

        Route::post(
            '/pemilik/pembayaran/store',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'store']
        )->name('pemilik.pembayaran.store');

        Route::delete(
            '/pemilik/pembayaran/{id}',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'destroy']
        )->name('pemilik.pembayaran.destroy');
        Route::get(
            '/pemilik/pembayaran/{id}/edit',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'edit']
        )->name('pemilik.pembayaran.edit');

        Route::put(
            '/pemilik/pembayaran/{id}',
            [App\Http\Controllers\Pemilik\MetodePembayaranController::class, 'update']
        )->name('pemilik.pembayaran.update');
        // ================= VERIFIKASI PEMBAYARAN =================
        Route::get(
            '/pemilik/verifikasi',
            [App\Http\Controllers\Pemilik\VerifikasiPembayaranController::class, 'index']
        )->name('pemilik.verifikasi.index');

        Route::post(
            '/pemilik/verifikasi/{id}/konfirmasi',
            [App\Http\Controllers\Pemilik\VerifikasiPembayaranController::class, 'konfirmasi']
        )->name('pemilik.verifikasi.konfirmasi');

        Route::post(
            '/pemilik/verifikasi/{id}/tolak',
            [App\Http\Controllers\Pemilik\VerifikasiPembayaranController::class, 'tolak']
        )->name('pemilik.verifikasi.tolak');
    });

    // punya penyewa
    Route::middleware('role:penyewa')->group(function () {
        Route::get(
            '/penyewa/dashboard',
            [
                PenyewaKosController::class,
                'index'
            ]
        )->name('penyewa.dashboard');;
        Route::get(
            '/penyewa/kos/{id}',
            [PenyewaKosController::class, 'show']
        )->name('penyewa.kos.detail');
        Route::get('/penyewa/rekomendasi', [RecommendationController::class, 'index'])
            ->name('penyewa.rekomendasi');
        Route::get(
            '/penyewa/cari-kos',
            [PenyewaKosController::class, 'search']
        )->name('penyewa.cari.kos');
        Route::post(
            '/penyewa/pengajuan',
            [App\Http\Controllers\Penyewa\PengajuanController::class, 'store']
        )->name('penyewa.pengajuan.store');
        Route::get(
            '/penyewa/pengajuan',
            [App\Http\Controllers\Penyewa\PengajuanController::class, 'index']
        )->name('penyewa.pengajuan.index');
        Route::post(
            '/penyewa/bayar/{id}',
            [App\Http\Controllers\Penyewa\PembayaranController::class, 'store']
        )->name('penyewa.bayar');
        // ================= RIWAYAT PEMBAYARAN =================
        Route::get(
            '/penyewa/pembayaran',
            [PembayaranController::class, 'index']
        )->name('penyewa.pembayaran.index');

        // AJUKAN ULANG
        Route::post(
            '/penyewa/pembayaran/{id}/ajukan-ulang',
            [PembayaranController::class, 'ajukanUlang']
        )->name('penyewa.pembayaran.ajukan-ulang');
        // PROFILE PENYEWA
        Route::get('/penyewa/profile', [ProfilePenyewaController::class, 'index'])
            ->name('penyewa.profile');

        Route::post('/penyewa/profile', [ProfilePenyewaController::class, 'update'])
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
