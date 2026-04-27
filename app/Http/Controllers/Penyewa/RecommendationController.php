<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Services\RecommendationScoringService;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationScoringService $recommendationScoring) {}

    public function index()
    {
        $user = Auth::user();
        $rekomendasi = $this->recommendationScoring->rankForUser($user, 10);

        return view('penyewa.rekomendasi', compact('rekomendasi'));
    }
}
