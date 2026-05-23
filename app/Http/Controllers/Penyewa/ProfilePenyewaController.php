<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class ProfilePenyewaController extends Controller
{
    public function index()
    {
        return view('penyewa.profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|confirmed|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('profile', 'public');
            $user->photo = $path;
        }

        $user->no_hp = $request->no_hp;
        $user->email = $request->email;
        $user->name = $request->name;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile berhasil diperbarui');
    }
    // ================= HAPUS FOTO =================
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo) {

            if (Storage::exists('public/' . $user->photo)) {
                Storage::delete('public/' . $user->photo);
            }

            $user->photo = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto berhasil dihapus');
    }
}
