<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\UserPreference;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pref = UserPreference::where('user_id', $user->id)->first();

        // Jika belum punya histori, jangan kasih rekomendasi
        if (!$pref) {
            $rekomendasi = collect([]);
            return view('penyewa.rekomendasi', compact('rekomendasi'));
        }

        $semuaKos = Kos::with('kamars')->where('status', 'disetujui')->get();

        $rekomendasi = $semuaKos->map(function ($kos) use ($pref) {
            $score = $this->calculateSimilarity($pref, $kos);
            $kos->similarity_score = $score * 100;
            $kos->label = $this->getLabel($kos->similarity_score);
            return $kos;
        })->sortByDesc('similarity_score')->take(10);

        return view('penyewa.rekomendasi', compact('rekomendasi'));
    }

    private function calculateSimilarity($pref, $kos)
    {
        // Deklarasi bobot untuk setiap kriteria
        $wHarga = 0.35;
        $wTipe = 0.25;
        $wFasilitas = 0.25;
        $wTipeHarga = 0.15;

        $kamar = $kos->kamars->sortBy('harga')->first();

        # filter harga: jika harga kamar <= preferensi harga, nilai 1 (cocok sempurna), jika di atas, hitung rasio (pref / harga)
        $cHarga = 0;
        if ($kamar && $pref->pref_harga > 0) {
            if ($kamar->harga <= $pref->pref_harga) {
                // Sesuai budget = 1 (Sempurna)
                $cHarga = 1;
            } else {
                // Di luar budget, hitung rasio jaraknya
                $cHarga = $pref->pref_harga / $kamar->harga;
            }
        }

        # filter tipe kos: jika tipe kos sama dengan preferensi, nilai 1, jika berbeda, nilai 0.33 (masih satu jenis kos)
        $cTipe = 0;
        if ($pref->pref_tipe_kos) {
            if (strtolower($kos->tipe_kos) == strtolower($pref->pref_tipe_kos)) {
                $cTipe = 1; // Cocok sempurna
            } else {
                $cTipe = 0.33;
            }
        } else {
            $cTipe = 1;
        }

        # filter fasilitas: hitung rasio kecocokan antara fasilitas kamar dan preferensi fasilitas user
        $userFas = is_array($pref->pref_fasilitas) ? $pref->pref_fasilitas : [];
        // Ambil array JSON dari tabel kamars
        $kamarFas = $kamar && is_array($kamar->fasilitas) ? $kamar->fasilitas : [];

        $cFasilitas = 0;
        if (count($userFas) > 0) {
            $intersect = array_intersect($userFas, $kamarFas);
            // Hitung rasio kecocokan (jumlah cocok / jumlah yg diminta)
            $cFasilitas = count($intersect) / count($userFas);
        } else {
            // Jika user tidak milih fasilitas apa-apa, netral/cocok
            $cFasilitas = 1;
        }

        # filter tipe harga
        $cTipeHarga = 0;
        if ($pref->pref_tipe_harga && $kamar) {
            if (strtolower($kamar->tipe_harga) == strtolower($pref->pref_tipe_harga)) {
                $cTipeHarga = 1;
            } else {
                // Beda tipe bayar (Bulanan vs Tahunan), 1 dibagi 2 jenis = 0.5
                $cTipeHarga = 0.5;
            }
        } else {
            $cTipeHarga = 1;
        }

        # skor akhir = jumlah dari (nilai kriteria * bobot)
        return ($cHarga * $wHarga) + ($cTipe * $wTipe) + ($cFasilitas * $wFasilitas) + ($cTipeHarga * $wTipeHarga);
    }

    private function getLabel($score)
    {
        if ($score >= 90) return 'Sangat Cocok';
        if ($score >= 80) return 'Sesuai Preferensi';
        if ($score >= 70) return 'Cocok';
        if ($score >= 60) return 'Mungkin Cocok';
        return 'Kurang Sesuai';
    }
}
