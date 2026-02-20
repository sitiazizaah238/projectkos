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

    // update profile
   public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
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

    // ================= DATA =================
    $user->name = $request->name;
    $user->email = $request->email;

    // ================= PASSWORD =================
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();
return redirect()->route('pemilik.profile')
    ->with('success', 'Profile berhasil diperbarui');
}
}
