<?php

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\RecommendationScoringService;

function makeKosForRecommendation(User $pemilik, string $nama, int $harga, string $tipeKos, string $tipeHarga, array $fasilitas = []): Kos
{
    $kos = Kos::create([
        'user_id' => $pemilik->id,
        'nama_kos' => $nama,
        'lokasi' => 'Bandung',
        'tipe_kos' => $tipeKos,
        'status' => 'disetujui',
    ]);

    Kamar::create([
        'kos_id' => $kos->id,
        'nama_kamar' => 'Kamar ' . $nama,
        'harga' => $harga,
        'tipe_harga' => $tipeHarga,
        'status' => 'tersedia',
        'fasilitas' => $fasilitas,
    ]);

    return $kos;
}

it('ranks kos with normalized full criteria and excludes inactive owners', function () {
    $penyewa = User::factory()->create([
        'role' => 'penyewa',
        'status' => 'aktif',
    ]);

    $pemilikAktif = User::factory()->create([
        'role' => 'pemilik',
        'status' => 'aktif',
    ]);

    $pemilikNonaktif = User::factory()->create([
        'role' => 'pemilik',
        'status' => 'nonaktif',
    ]);

    UserPreference::create([
        'user_id' => $penyewa->id,
        'pref_harga' => 1000000,
        'pref_tipe_kos' => 'putri',
        'pref_fasilitas' => ['Wifi', 'CCTV'],
        'pref_tipe_harga' => 'bulanan',
    ]);

    $best = makeKosForRecommendation($pemilikAktif, 'Best', 900000, 'putri', 'bulanan', ['Wifi', 'CCTV']);
    makeKosForRecommendation($pemilikAktif, 'Medium', 1100000, 'campur', 'bulanan', ['Wifi']);
    makeKosForRecommendation($pemilikNonaktif, 'Ignored', 700000, 'putri', 'bulanan', ['Wifi', 'CCTV']);

    $service = app(RecommendationScoringService::class);
    $ranked = $service->rankForUser($penyewa, 10);

    expect($ranked->pluck('nama_kos')->contains('Ignored'))->toBeFalse();
    expect($ranked->first()->id)->toBe($best->id);
    expect($ranked->first()->similarity_score)->toBeGreaterThan($ranked->last()->similarity_score);
});

it('learns softly from view and strongly from confirmed action', function () {
    $penyewa = User::factory()->create([
        'role' => 'penyewa',
        'status' => 'aktif',
    ]);

    $pemilik = User::factory()->create([
        'role' => 'pemilik',
        'status' => 'aktif',
    ]);

    $kosBulanan = makeKosForRecommendation($pemilik, 'Bulanan', 800000, 'campur', 'bulanan', ['Wifi']);
    $kosTahunan = makeKosForRecommendation($pemilik, 'Tahunan', 12000000, 'putri', 'tahunan', ['Wifi', 'CCTV']);

    UserPreference::create([
        'user_id' => $penyewa->id,
        'pref_harga' => 1000000,
        'pref_tipe_kos' => 'campur',
        'pref_fasilitas' => ['Wifi'],
        'pref_tipe_harga' => 'bulanan',
    ]);

    $service = app(RecommendationScoringService::class);

    $service->learnFromKosView($penyewa, $kosTahunan);
    $prefAfterView = UserPreference::where('user_id', $penyewa->id)->first();

    expect($prefAfterView->pref_tipe_kos)->toBe('campur,putri');
    expect($prefAfterView->pref_tipe_harga)->toBe('bulanan,tahunan');
    expect((int) $prefAfterView->pref_harga)->toBeGreaterThan(1000000);

    $kamarTahunan = $kosTahunan->kamars()->first();
    $service->learnFromConfirmedAction($penyewa, $kosTahunan, $kamarTahunan);

    $prefAfterAction = UserPreference::where('user_id', $penyewa->id)->first();

    expect($prefAfterAction->pref_tipe_kos)->toBe('campur,putri,putri');
    expect($prefAfterAction->pref_tipe_harga)->toBe('bulanan,tahunan,tahunan');
    expect($prefAfterAction->pref_fasilitas)->toContain('CCTV');

    // Ensure existing unrelated kos still present and service can rank post-learning.
    $ranked = $service->rankForUser($penyewa, 2);
    expect($ranked->pluck('nama_kos')->all())->toContain('Bulanan');
});
