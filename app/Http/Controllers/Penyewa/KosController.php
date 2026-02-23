<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosController extends Controller
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

        $semuaKos = Kos::with(['kamars' => function ($q) {
            $q->where('status', 'tersedia');
        }])
            ->where('status', 'disetujui')
            ->whereHas('kamars', function ($q) {
                $q->where('status', 'tersedia');
            })
            ->get();

        $rekomendasi = $semuaKos->map(function ($kos) use ($pref) {
            $score = $this->calculateSimilarity($pref, $kos);
            $kos->similarity_score = $score * 100;
            $kos->label = $this->getLabel($kos->similarity_score);
            return $kos;
        })->sortByDesc('similarity_score')->take(limit: 3);

        return view('penyewa.dashboard', compact('rekomendasi'));
    }

    public function search(Request $request)
    {
        $search = $request->search;

        // Debugging
        // if ($request->filled('fasilitas')) {
        //     $rawKamars = \App\Models\Kamar::select('id', 'fasilitas')->limit(5)->get();
        //     dd([
        //         'request_fasilitas' => $request->fasilitas,
        //         'raw_db_sample' => $rawKamars->toArray(),
        //     ]);
        // }

        $kos = Kos::with('kamars')
            ->where('status', 'disetujui')
            // Filter Search (Nama & Lokasi)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // 1. Full phrase selalu dicoba (prioritas utama)
                    $q->where('nama_kos', 'like', "%$search%")
                        ->orWhere('lokasi', 'like', "%$search%");

                    // 2. Strip kata umum kos/kost/kostel, ambil sisa kata bermakna
                    $stopWords = ['kos', 'kost', 'kostel', 'kontrakan'];
                    $keywords = array_filter(explode(' ', trim(strtolower($search))));
                    $meaningful = array_filter($keywords, function ($word) use ($stopWords) {
                        return strlen($word) >= 3 && !in_array($word, $stopWords);
                    });

                    // 3. Fallback per-kata HANYA dari kata bermakna
                    if (count($meaningful) > 0) {
                        foreach ($meaningful as $word) {
                            $q->orWhere('nama_kos', 'like', "%$word%")
                                ->orWhere('lokasi', 'like', "%$word%");
                        }
                    }

                    // 4. Tanpa spasi (untuk typo spasi)
                    $noSpace = str_replace(' ', '', $search);
                    if (strlen($noSpace) >= 3) {
                        $q->orWhereRaw("REPLACE(nama_kos, ' ', '') LIKE ?", ["%$noSpace%"])
                            ->orWhereRaw("REPLACE(lokasi, ' ', '') LIKE ?", ["%$noSpace%"]);
                    }
                });
            })
            // Filter Search (Nama & Lokasi)
            ->when($request->filled('fasilitas'), function ($query) use ($request) {
                $query->whereHas('kamars', function ($q) use ($request) {
                    foreach ($request->fasilitas as $fasilitas) {
                        // JSON_CONTAINS lebih reliable daripada LIKE untuk kolom JSON
                        $q->whereRaw('JSON_CONTAINS(fasilitas, ?)', [json_encode($fasilitas)]);
                    }
                });
            })
            // Filter Tipe Kos
            ->when($request->filled('tipe_kos'), function ($query) use ($request) {
                $query->where('tipe_kos', $request->tipe_kos);
            })
            // Filter Harga
            ->when($request->filled('min_harga') || $request->filled('max_harga'), function ($query) use ($request) {
                $query->whereHas('kamars', function ($q) use ($request) {
                    if ($request->filled('min_harga')) {
                        $q->where('harga', '>=', $request->min_harga);
                    }
                    if ($request->filled('max_harga')) {
                        $q->where('harga', '<=', $request->max_harga);
                    }
                });
            })
            // Filter Tipe Harga
            ->when($request->filled('tipe_harga'), function ($query) use ($request) {
                $query->whereHas('kamars', function ($q) use ($request) {
                    $q->where('tipe_harga', $request->tipe_harga);
                });
            })
            // Filter Fasilitas (Pencarian Raw JSON String)
            ->when($request->filled('fasilitas'), function ($query) use ($request) {
                $query->whereHas('kamars', function ($q) use ($request) {
                    foreach ($request->fasilitas as $fasilitas) {
                        // Karena JSON disimpan sebagai ["ac", "meja"], 
                        // kita cari menggunakan LIKE yang diapit tanda kutip ganda
                        $q->where('fasilitas', 'LIKE', '%"' . $fasilitas . '"%');
                    }
                });
            })
            ->latest()
            ->get();

        // SIMPAN PREFERENSI DARI HASIL PENCARIAN (bukan hanya dari filter)
        if ($search && $kos->isNotEmpty()) {
            $pref = UserPreference::firstOrNew(['user_id' => Auth::id()]);

            // Ambil kos pertama dari hasil search sebagai referensi preferensi
            $kosReferensi = $kos->first();
            $kamarReferensi = $kosReferensi->kamars->sortBy('harga')->first();

            // Pelajari tipe kos dari hasil pencarian
            if (!$pref->pref_tipe_kos) {
                $pref->pref_tipe_kos = $kosReferensi->tipe_kos;
            }

            if ($kamarReferensi) {
                // Pelajari fasilitas (gabung dengan yang lama)
                if (is_array($kamarReferensi->fasilitas)) {
                    $existing = is_array($pref->pref_fasilitas) ? $pref->pref_fasilitas : [];
                    $pref->pref_fasilitas = array_values(array_unique(array_merge($existing, $kamarReferensi->fasilitas)));
                }

                // Pelajari harga dan tipe harga (hanya jika belum ada preferensi)
                if (!$pref->pref_harga) {
                    $pref->pref_harga = $kamarReferensi->harga;
                }
                if (!$pref->pref_tipe_harga) {
                    $pref->pref_tipe_harga = $kamarReferensi->tipe_harga;
                }
            }

            $pref->save();
        }

        return view('penyewa.cari', compact('kos', 'search'));
    }

    // fungsi untuk menyimpan preferensi user berdasarkan kos yang dilihat
    public function show($id)
    {
        $kos = Kos::with(['kamars' => function ($query) {
            $query->where('status', 'tersedia');
        }])
            ->where('status', 'disetujui')
            ->findOrFail($id);

        $pref = UserPreference::firstOrNew(['user_id' => Auth::id()]);

        // mencatat tipe kos yang sering dilihat
        $pref->pref_tipe_kos = $kos->tipe_kos;

        // Ambil fasilitas dari kamar
        $kamarTermurah = $kos->kamars->sortBy('harga')->first();
        if ($kamarTermurah && is_array($kamarTermurah->fasilitas)) {
            $existing = is_array($pref->pref_fasilitas) ? $pref->pref_fasilitas : [];
            $pref->pref_fasilitas = array_values(array_unique(array_merge($existing, $kamarTermurah->fasilitas)));
        }

        // Mencatat harga dan tipe harga dari kamar termurah yang dilihat
        if ($kamarTermurah) {
            $pref->pref_harga = $kamarTermurah->harga;
            $pref->pref_tipe_harga = $kamarTermurah->tipe_harga;
        }

        $pref->save();

        return view('penyewa.detail', compact('kos'));
    }

    private function calculateSimilarity($pref, $kos)
    {
        // Deklarasi bobot untuk setiap kriteria
        $wHarga = 0.35;
        $wTipe = 0.25;
        $wFasilitas = 0.25;
        $wTipeHarga = 0.15;

        $kamar = $kos->kamars->where('status', 'tersedia')->sortBy('harga')->first();

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
        if ($score >= 50) return 'Mungkin Cocok';
        return 'Kurang Sesuai';
    }
}
