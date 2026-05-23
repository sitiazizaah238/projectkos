<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class ProfilePemilikController extends Controller
{
    // tampil halaman profile
    public function index()
    {
        return view('pemilik.profile', [
            'user' => Auth::user()
        ]);
    }

public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'no_hp' => 'nullable|string|max:15', // ✅ tambahin ini
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'password' => 'nullable|confirmed|min:6',
    ]);

    // ================= FOTO =================
    if ($request->hasFile('photo')) {

        // hapus foto lama
        if ($user->photo && Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        // upload foto baru
        $file = $request->file('photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('profile', $filename, 'public');

        $user->photo = $filename;
    }

    // ================= FORMAT NO HP =================
    $no_hp = $request->no_hp;

    if ($no_hp) {
        $no_hp = preg_replace('/[^0-9]/', '', $no_hp); // hanya angka

        if (substr($no_hp, 0, 1) == '0') {
            $no_hp = '62' . substr($no_hp, 1);
        }
    }

    // ================= DATA =================
    $user->name = $request->name;
    $user->email = $request->email;
    $user->no_hp = $no_hp; // ✅ simpan no hp

    // ================= PASSWORD =================
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('pemilik.profile')
        ->with('success', 'Profile berhasil diperbarui');
}
public function deletePhoto()
{
    $user = Auth::user();

    // cek foto ada
    if ($user->photo) {

        // hapus file storage
        if (Storage::exists('public/profile/' . $user->photo)) {
            Storage::delete('public/profile/' . $user->photo);
        }

        // hapus database
        $user->photo = null;
        $user->save();
    }

    return redirect()->route('pemilik.profile')
        ->with('success', 'Foto berhasil dihapus');
}
}
