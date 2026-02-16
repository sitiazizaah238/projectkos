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
        'email' => 'required|email|unique:users',
        'no_hp' => 'required',
        'alamat' => 'required',
        'password' => 'required|min:6|confirmed'
    ]);

    User::create([
        'name'   => $r->name,
        'email'  => $r->email,
        'no_hp'  => $r->no_hp,
        'alamat' => $r->alamat,
        'password' => bcrypt($r->password),
        'role'   => 'pemilik',
        'status'   => 'aktif'
    ]);

    return redirect('/login')->with('success', 'Registrasi berhasil');
}


    public function regPenyewa() {
        return view('auth.reg-penyewa');
    }

    public function storePenyewa(Request $r) {
        User::create([
            'name'=>$r->name,
            'email'=>$r->email,
            'no_hp'=>$r->no_hp,
            'password'=>bcrypt($r->password),
            'role'=>'penyewa'
        ]);
        return redirect('/login');
    }
}
