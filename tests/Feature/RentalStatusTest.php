<?php

use App\Models\User;
use App\Models\Kos;
use App\Models\Kamar;
use App\Models\PengajuanSewa;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->pemilik = User::factory()->create(['role' => 'pemilik']);
    $this->kos = Kos::create([
        'user_id' => $this->pemilik->id,
        'nama_kos' => 'Kos Testing',
        'tipe_kos' => 'putra',
        'lokasi' => 'Lohbener',
        'status' => 'disetujui'
    ]);
    $this->kamar = Kamar::create([
        'kos_id' => $this->kos->id,
        'nama_kamar' => 'A1',
        'harga' => 500000,
        'status' => 'tersedia',
        'tipe_harga' => 'bulanan'
    ]);
});

test('sewa baru harus berstatus aktif', function () {
    $pengajuan = PengajuanSewa::create([
        'user_id' => $this->user->id,
        'kos_id' => $this->kos->id,
        'kamar_id' => $this->kamar->id,
        'tanggal_mulai' => now(),
        'durasi' => 1,
        'status' => 'aktif'
    ]);

    expect($pengajuan->statusSaatIni())->toBe('aktif');
});

test('sewa h-5 harus berstatus jatuh tempo', function () {
    // Set agar 1 bulan dari sekarang adalah H-5 (berarti tgl selesai adalah hari ini + 5 hari)
    $tanggalMulai = now()->subMonth()->addDays(5);
    
    $pengajuan = PengajuanSewa::create([
        'user_id' => $this->user->id,
        'kos_id' => $this->kos->id,
        'kamar_id' => $this->kamar->id,
        'tanggal_mulai' => $tanggalMulai,
        'durasi' => 1,
        'status' => 'aktif'
    ]);

    expect($pengajuan->sisaHariSewa())->toBe(5);
    expect($pengajuan->statusSaatIni())->toBe('jatuh_tempo');
});

test('sewa h+1 (masa tenggang) harus tetap jatuh tempo', function () {
    // Set agar masa sewa habis kemarin (H+1)
    $tanggalMulai = now()->subMonth()->subDay();
    
    $pengajuan = PengajuanSewa::create([
        'user_id' => $this->user->id,
        'kos_id' => $this->kos->id,
        'kamar_id' => $this->kamar->id,
        'tanggal_mulai' => $tanggalMulai,
        'durasi' => 1,
        'status' => 'aktif'
    ]);

    expect($pengajuan->sisaHariSewa())->toBe(-1);
    expect($pengajuan->statusSaatIni())->toBe('jatuh_tempo');
});

test('sewa h+3 harus otomatis selesai dan kamar tersedia', function () {
    // Set agar masa sewa habis 3 hari yang lalu
    $tanggalMulai = now()->subMonth()->subDays(3);
    
    $pengajuan = PengajuanSewa::create([
        'user_id' => $this->user->id,
        'kos_id' => $this->kos->id,
        'kamar_id' => $this->kamar->id,
        'tanggal_mulai' => $tanggalMulai,
        'durasi' => 1,
        'status' => 'aktif'
    ]);

    $this->kamar->update(['status' => 'terisi']);

    // Jalankan sinkronisasi
    PengajuanSewa::syncExpiredRentals();

    $pengajuan->refresh();
    $this->kamar->refresh();

    expect($pengajuan->status)->toBe('selesai');
    expect($this->kamar->status)->toBe('tersedia');
});
