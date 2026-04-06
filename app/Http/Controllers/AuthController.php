<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function regPemilik() {
        return view('auth.reg-pemilik');
    }

 public function storePemilik(Request $r)
{
    $r->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'no_hp' => 'required',
        'alamat' => 'required',
        'password' => 'required|min:6|confirmed',
    ], [
        'name.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'no_hp.required' => 'No HP wajib diisi',
        'alamat.required' => 'Alamat wajib diisi',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'password.confirmed' => 'Konfirmasi password tidak sama',
    ]);

    User::create([
        'name'   => $r->name,
        'email'  => $r->email,
        'no_hp'  => $r->no_hp,
        'alamat' => $r->alamat,
        'password' => bcrypt($r->password),
        'role'   => 'pemilik',
    ]);

    return redirect('/login')->with('success', 'Registrasi berhasil');
}


    public function regPenyewa() {
        return view('auth.reg-penyewa');
    }

   public function storePenyewa(Request $r)
{
    // VALIDASI DULU
    $r->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'no_hp' => 'required',
        'password' => 'required|min:6|confirmed',
    ], [
        'name.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'no_hp.required' => 'No HP wajib diisi',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'password.confirmed' => 'Konfirmasi password tidak sama',
    ]);

    // SIMPAN DATA
    User::create([
        'name' => $r->name,
        'email' => $r->email,
        'no_hp' => $r->no_hp,
        'password' => bcrypt($r->password),
        'role' => 'penyewa'
    ]);

    return redirect('/login')->with('success', 'Registrasi berhasil');
}
}
