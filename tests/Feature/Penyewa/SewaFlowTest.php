<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\MetodePembayaran;
use App\Models\Pembayaran;
use App\Models\PengajuanSewa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeSewaFixture(string $tipeHarga = 'bulanan', int $harga = 1000000): array
{
    $pemilik = User::factory()->create([
        'role' => 'pemilik',
        'status' => 'aktif',
    ]);

    $penyewa = User::factory()->create([
        'role' => 'penyewa',
        'status' => 'aktif',
    ]);

    $kos = Kos::create([
        'user_id' => $pemilik->id,
        'nama_kos' => 'Kos Test',
        'lokasi' => 'Bandung',
        'tipe_kos' => 'Campur',
        'status' => 'disetujui',
    ]);

    $kamar = Kamar::create([
        'kos_id' => $kos->id,
        'nama_kamar' => 'A-01',
        'harga' => $harga,
        'tipe_harga' => $tipeHarga,
        'status' => 'tersedia',
    ]);

    $metode = MetodePembayaran::create([
        'user_id' => $pemilik->id,
        'nama_metode' => 'BCA',
        'atas_nama' => 'Pemilik Test',
        'no_rekening' => '1234567890',
        'status' => 'aktif',
    ]);

    return compact('pemilik', 'penyewa', 'kos', 'kamar', 'metode');
}

function makePengajuan(array $fixture, array $overrides = []): PengajuanSewa
{
    $kamar = $fixture['kamar'];
    $durasi = $overrides['durasi'] ?? ($kamar->tipe_harga === 'tahunan' ? 12 : 1);

    if ($kamar->tipe_harga === 'tahunan') {
        $totalBayar = (int) (($kamar->harga / 12) * $durasi);
    } else {
        $totalBayar = (int) ($kamar->harga * $durasi);
    }

    return PengajuanSewa::create(array_merge([
        'user_id' => $fixture['penyewa']->id,
        'kos_id' => $fixture['kos']->id,
        'kamar_id' => $kamar->id,
        'tanggal_mulai' => now()->toDateString(),
        'durasi' => $durasi,
        'status' => 'disetujui',
        'jenis_pengajuan' => 'sewa_baru',
        'total_bayar' => $totalBayar,
    ], $overrides));
}

it('stores monthly submission with correct total', function () {
    $fixture = makeSewaFixture('bulanan', 750000);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.pengajuan.store'), [
            'kos_id' => $fixture['kos']->id,
            'kamar_id' => $fixture['kamar']->id,
            'tanggal_mulai' => now()->toDateString(),
            'jenis_sewa' => 'bulanan',
            'durasi' => 3,
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pengajuan_sewas', [
        'user_id' => $fixture['penyewa']->id,
        'kamar_id' => $fixture['kamar']->id,
        'durasi' => 3,
        'total_bayar' => 2250000,
    ]);
});

it('stores yearly submission with one year total when duration is 12 months', function () {
    $fixture = makeSewaFixture('tahunan', 12000000);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.pengajuan.store'), [
            'kos_id' => $fixture['kos']->id,
            'kamar_id' => $fixture['kamar']->id,
            'tanggal_mulai' => now()->toDateString(),
            'jenis_sewa' => 'tahunan',
            'durasi' => 12,
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pengajuan_sewas', [
        'user_id' => $fixture['penyewa']->id,
        'kamar_id' => $fixture['kamar']->id,
        'durasi' => 12,
        'total_bayar' => 12000000,
    ]);
});

it('allows yearly submission with custom duration and pro-rated price', function () {
    $fixture = makeSewaFixture('tahunan', 12000000);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.pengajuan.store'), [
            'kos_id' => $fixture['kos']->id,
            'kamar_id' => $fixture['kamar']->id,
            'tanggal_mulai' => now()->toDateString(),
            'jenis_sewa' => 'tahunan',
            'durasi' => 6,
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pengajuan_sewas', [
        'user_id' => $fixture['penyewa']->id,
        'kamar_id' => $fixture['kamar']->id,
        'durasi' => 6,
        'total_bayar' => 6000000, // 12M / 12 * 6
    ]);
});

it('extends yearly rental with yearly price increment', function () {
    $fixture = makeSewaFixture('tahunan', 12000000);

    $pengajuan = makePengajuan($fixture, [
        'durasi' => 12,
        'status' => 'selesai',
        'total_bayar' => 12000000,
    ]);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.pengajuan.perpanjang', $pengajuan->id), [
            'durasi_tambahan' => 12,
        ])
        ->assertRedirect(route('penyewa.pengajuan.index', ['focus_bayar' => $pengajuan->id]));

    $pengajuan->refresh();

    expect((int) $pengajuan->durasi)->toBe(24)
        ->and((int) $pengajuan->total_bayar)->toBe(24000000)
        ->and($pengajuan->status)->toBe('disetujui');
});

it('allows yearly extension with custom duration and pro-rated price', function () {
    $fixture = makeSewaFixture('tahunan', 12000000);

    $pengajuan = makePengajuan($fixture, [
        'durasi' => 12,
        'status' => 'selesai',
        'total_bayar' => 12000000,
    ]);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.pengajuan.perpanjang', $pengajuan->id), [
            'durasi_tambahan' => 3,
        ])
        ->assertRedirect(route('penyewa.pengajuan.index', ['focus_bayar' => $pengajuan->id]));

    $pengajuan->refresh();

    expect((int) $pengajuan->durasi)->toBe(15)
        ->and((int) $pengajuan->total_bayar)->toBe(15000000); // 12M + (12M/12 * 3)
});

it('creates yearly payment with nominal based on year count', function () {
    Storage::fake('public');

    $fixture = makeSewaFixture('tahunan', 12000000);
    $pengajuan = makePengajuan($fixture, [
        'durasi' => 24,
        'status' => 'disetujui',
    ]);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.bayar', $pengajuan->id), [
            'metode_id' => $fixture['metode']->id,
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pembayarans', [
        'pengajuan_sewa_id' => $pengajuan->id,
        'durasi_tagihan' => 24,
        'nominal_tagihan' => 24000000,
        'status' => 'menunggu',
    ]);
});

it('creates monthly payment with monthly nominal multiplication', function () {
    Storage::fake('public');

    $fixture = makeSewaFixture('bulanan', 800000);
    $pengajuan = makePengajuan($fixture, [
        'durasi' => 3,
        'status' => 'disetujui',
    ]);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.bayar', $pengajuan->id), [
            'metode_id' => $fixture['metode']->id,
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pembayarans', [
        'pengajuan_sewa_id' => $pengajuan->id,
        'durasi_tagihan' => 3,
        'nominal_tagihan' => 2400000,
        'status' => 'menunggu',
    ]);
});

it('allows yearly payment with pro-rated nominal for custom duration', function () {
    Storage::fake('public');

    $fixture = makeSewaFixture('tahunan', 12000000);
    $pengajuan = makePengajuan($fixture, [
        'durasi' => 18,
        'status' => 'disetujui',
    ]);

    $this->actingAs($fixture['penyewa'])
        ->post(route('penyewa.bayar', $pengajuan->id), [
            'metode_id' => $fixture['metode']->id,
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ])
        ->assertRedirect(route('penyewa.pengajuan.index'));

    $this->assertDatabaseHas('pembayarans', [
        'pengajuan_sewa_id' => $pengajuan->id,
        'nominal_tagihan' => 18000000, // 12M / 12 * 18
        'status' => 'menunggu',
    ]);
});
