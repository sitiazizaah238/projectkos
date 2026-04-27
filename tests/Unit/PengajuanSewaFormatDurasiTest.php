<?php

use App\Models\PengajuanSewa;

it('formats monthly duration label correctly', function () {
    expect(PengajuanSewa::formatDurasiByTipe(3, 'bulanan'))->toBe('3 Bulan');
});

it('formats yearly duration label correctly when divisible by 12', function () {
    expect(PengajuanSewa::formatDurasiByTipe(12, 'tahunan'))->toBe('1 Tahun')
        ->and(PengajuanSewa::formatDurasiByTipe(24, 'tahunan'))->toBe('2 Tahun');
});

it('falls back to month label for yearly type when duration is not divisible by 12', function () {
    expect(PengajuanSewa::formatDurasiByTipe(6, 'tahunan'))->toBe('6 Bulan');
});
