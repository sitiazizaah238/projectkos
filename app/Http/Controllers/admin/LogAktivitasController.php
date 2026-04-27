<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogAktivitasController extends Controller
{

public function index(Request $request)
{
    $filter = $request->filter; // 7, 30, all
    $search = $request->search;

    $query = LogAktivitas::with(['user', 'kos'])->latest();

    // 🔹 FILTER WAKTU
    if ($filter == '7') {
        $query->where('created_at', '>=', Carbon::now()->subDays(7));
    } elseif ($filter == '30') {
        $query->where('created_at', '>=', Carbon::now()->subDays(30));
    }
    // kalau 'all' atau null = tampilkan semua

    // 🔹 SEARCH
    if ($search) {
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    $logs = $query->paginate(5)->withQueryString();

    return view('admin.log.index', compact('logs', 'filter'));
}
}
