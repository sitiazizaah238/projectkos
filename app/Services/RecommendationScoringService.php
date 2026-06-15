<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Collection;

class RecommendationScoringService
{
    private const WEIGHT_HARGA = 0.35;
    private const WEIGHT_TIPE_KOS = 0.25;
    private const WEIGHT_FASILITAS = 0.25;
    private const WEIGHT_TIPE_HARGA = 0.15;

    public function rankForUser(User $user, int $limit = 10): Collection
    {
        $pref = UserPreference::where('user_id', $user->id)->first();

        if (! $pref) {
            // Default preference for new users (prefer cheap, campur, any duration)
            $pref = new UserPreference([
                'pref_harga' => 1000000,
                'pref_tipe_kos' => 'Campur',
                'pref_tipe_harga' => 'Bulanan',
                'pref_fasilitas' => []
            ]);
        }

        $kandidatKos = Kos::with([
            'kamars' => function ($q) {
                $q->where('status', 'tersedia');
            },
        ])
            ->where('status', 'disetujui')
            ->whereHas('user', function ($q) {
                $q->where('status', 'aktif');
            })
            ->whereHas('kamars', function ($q) {
                $q->where('status', 'tersedia');
            })
            ->get();

        if ($kandidatKos->isEmpty()) {
            return collect();
        }

        return $this->scoreCandidates($kandidatKos, $pref)
            ->sortByDesc('similarity_score')
            ->take($limit)
            ->values();
    }

    public function learnFromSearchResult(User $user, Kos $kos): void
    {
        $kamar = $this->findCheapestAvailableRoom($kos);

        if (! $kamar) {
            return;
        }

        $this->learn($user, $kos, $kamar, 0.12);
    }

    public function learnFromKosView(User $user, Kos $kos): void
    {
        $kamar = $this->findCheapestAvailableRoom($kos);

        if (! $kamar) {
            return;
        }

        $this->learn($user, $kos, $kamar, 0.20);
    }

    public function learnFromConfirmedAction(User $user, Kos $kos, ?Kamar $kamar = null): void
    {
        $kamarDipakai = $kamar ?: $this->findCheapestAvailableRoom($kos);

        if (! $kamarDipakai) {
            return;
        }

        $this->learn($user, $kos, $kamarDipakai, 0.45);
    }

    private function scoreCandidates(Collection $kandidatKos, UserPreference $pref): Collection
    {
        $raw = $kandidatKos
            ->map(function (Kos $kos) use ($pref) {
                $kamar = $this->findCheapestAvailableRoom($kos);

                if (! $kamar) {
                    return null;
                }

                return [
                    'kos' => $kos,
                    'kamar' => $kamar,
                    'harga_diff' => abs((int) $kamar->harga - (int) $pref->pref_harga),
                    'tipe_kos' => $this->rawTipeKosScore($pref, $kos),
                    'fasilitas' => $this->rawFasilitasScore($pref, $kamar),
                    'tipe_harga' => $this->rawTipeHargaScore($pref, $kamar),
                ];
            })
            ->filter()
            ->values();

        if ($raw->isEmpty()) {
            return collect();
        }

        $normHarga = $this->normalizeCost($raw->pluck('harga_diff')->all());
        $normTipeKos = $this->normalizeBenefit($raw->pluck('tipe_kos')->all());
        $normFasilitas = $this->normalizeBenefit($raw->pluck('fasilitas')->all());
        $normTipeHarga = $this->normalizeBenefit($raw->pluck('tipe_harga')->all());

        return $raw->map(function (array $item, int $idx) use ($normHarga, $normTipeKos, $normFasilitas, $normTipeHarga) {
            $score = ($normHarga[$idx] * self::WEIGHT_HARGA)
                + ($normTipeKos[$idx] * self::WEIGHT_TIPE_KOS)
                + ($normFasilitas[$idx] * self::WEIGHT_FASILITAS)
                + ($normTipeHarga[$idx] * self::WEIGHT_TIPE_HARGA);

            /** @var Kos $kos */
            $kos = $item['kos'];
            $kos->similarity_score = $score * 100;
            $kos->label = $this->labelFor($kos->similarity_score);

            return $kos;
        });
    }

    private function rawTipeKosScore(UserPreference $pref, Kos $kos): float
    {
        if (! $pref->pref_tipe_kos) {
            return 1.0;
        }

        $history = array_filter(explode(',', strtolower((string) $pref->pref_tipe_kos)));
        if (empty($history)) {
            return 1.0;
        }

        $target = trim(strtolower((string) $kos->tipe_kos));
        $count = 0;
        foreach ($history as $h) {
            if (trim($h) === $target) {
                $count++;
            }
        }

        return $count / count($history);
    }

    private function rawFasilitasScore(UserPreference $pref, Kamar $kamar): float
    {
        $userFasilitas = is_array($pref->pref_fasilitas) ? $pref->pref_fasilitas : [];
        $kamarFasilitas = is_array($kamar->fasilitas) ? $kamar->fasilitas : [];

        if (count($userFasilitas) === 0) {
            return 1.0;
        }

        $cocok = array_intersect($userFasilitas, $kamarFasilitas);

        return count($cocok) / count($userFasilitas);
    }

    private function rawTipeHargaScore(UserPreference $pref, Kamar $kamar): float
    {
        if (! $pref->pref_tipe_harga) {
            return 1.0;
        }

        $history = array_filter(explode(',', strtolower((string) $pref->pref_tipe_harga)));
        if (empty($history)) {
            return 1.0;
        }

        $target = trim(strtolower((string) $kamar->tipe_harga));
        $count = 0;
        foreach ($history as $h) {
            if (trim($h) === $target) {
                $count++;
            }
        }

        return $count / count($history);
    }

    private function normalizeBenefit(array $values): array
    {
        if (count($values) === 0) {
            return [];
        }

        $min = min($values);
        $max = max($values);

        if ((float) $max === (float) $min) {
            return array_fill(0, count($values), 1.0);
        }

        return array_map(function ($value) use ($min, $max) {
            return ((float) $value - (float) $min) / ((float) $max - (float) $min);
        }, $values);
    }

    private function normalizeCost(array $values): array
    {
        if (count($values) === 0) {
            return [];
        }

        $min = min($values);
        $max = max($values);

        if ((float) $max === (float) $min) {
            return array_fill(0, count($values), 1.0);
        }

        return array_map(function ($value) use ($max, $min) {
            return ((float) $max - (float) $value) / ((float) $max - (float) $min);
        }, $values);
    }

    private function labelFor(float $score): string
    {
        if ($score >= 90) {
            return 'Sangat Cocok';
        }

        if ($score >= 80) {
            return 'Sesuai Preferensi';
        }

        if ($score >= 70) {
            return 'Cocok';
        }

        if ($score >= 50) {
            return 'Mungkin Cocok';
        }

        return 'Kurang Sesuai';
    }

    private function learn(User $user, Kos $kos, Kamar $kamar, float $alpha): void
    {
        $pref = UserPreference::firstOrNew(['user_id' => $user->id]);

        $hargaBaru = (int) $kamar->harga;
        $hargaLama = (int) ($pref->pref_harga ?? 0);

        if ($hargaLama <= 0) {
            $pref->pref_harga = $hargaBaru;
        } else {
            $pref->pref_harga = (int) round(($hargaLama * (1 - $alpha)) + ($hargaBaru * $alpha));
        }

        $kosTypes = array_filter(explode(',', strtolower((string) $pref->pref_tipe_kos)));
        $kosTypes[] = trim(strtolower($kos->tipe_kos));
        if (count($kosTypes) > 5) {
            array_shift($kosTypes);
        }
        $pref->pref_tipe_kos = implode(',', $kosTypes);

        $hargaTypes = array_filter(explode(',', strtolower((string) $pref->pref_tipe_harga)));
        $hargaTypes[] = trim(strtolower($kamar->tipe_harga));
        if (count($hargaTypes) > 5) {
            array_shift($hargaTypes);
        }
        $pref->pref_tipe_harga = implode(',', $hargaTypes);

        $existing = is_array($pref->pref_fasilitas) ? $pref->pref_fasilitas : [];
        $baru = is_array($kamar->fasilitas) ? $kamar->fasilitas : [];

        if (! empty($baru)) {
            $pref->pref_fasilitas = array_values(array_slice(array_unique(array_merge($baru, $existing)), 0, 12));
        }

        $pref->save();
    }

    private function findCheapestAvailableRoom(Kos $kos): ?Kamar
    {
        $kamars = $kos->relationLoaded('kamars')
            ? $kos->kamars
            : $kos->kamars()->where('status', 'tersedia')->get();

        /** @var Kamar|null $kamar */
        $kamar = $kamars->where('status', 'tersedia')->sortBy('harga')->first();

        return $kamar;
    }
}
