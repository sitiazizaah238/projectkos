<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Services\RecommendationScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosController extends Controller
{
    public function __construct(private RecommendationScoringService $recommendationScoring) {}

    public function index()
    {
        $user = Auth::user();
        $rekomendasi = $this->recommendationScoring->rankForUser($user, 3);

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
            ->whereHas('user', function ($q) {
                $q->where('status', 'aktif');
            })
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

        // Belajar dari hasil pencarian agar preferensi makin adaptif.
        if ($search && $kos->isNotEmpty()) {
            $this->recommendationScoring->learnFromSearchResult(Auth::user(), $kos->first());
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
            ->whereHas('user', function ($q) {
                $q->where('status', 'aktif');
            })
            ->findOrFail($id);

        $this->recommendationScoring->learnFromKosView(Auth::user(), $kos);

        return view('penyewa.detail', compact('kos'));
    }
}
