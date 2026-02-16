<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilePemilikController extends Controller
{
    // tampil halaman profile
    public function index()
    {
        return view('pemilik.profile', [
            'user' => Auth::user()
        ]);
    }

    // update profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success','Profile berhasil diupdate');
    }
}
