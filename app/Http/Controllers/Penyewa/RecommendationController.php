<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Services\RecommendationScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationScoringService $recommendationScoring) {}

  public function index(Request $request)
{
    $user = Auth::user();
    $hasilRekomendasi = collect(
        $this->recommendationScoring->rankForUser($user, 10)
    );

    // pagination
    $perPage = 6;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $hasilRekomendasi
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $rekomendasi = new LengthAwarePaginator(
        $currentItems,
        $hasilRekomendasi->count(),
        $perPage,
        $currentPage,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    return view('penyewa.rekomendasi', compact('rekomendasi'));
}
}
