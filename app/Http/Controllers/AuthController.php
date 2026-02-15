<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function regPemilik() {
        return view('auth.reg-pemilik');
    }

    public function storePemilik(Request $r) {
        User::create([
            'name'=>$r->name,
            'email'=>$r->email,
            'no_hp'=>$r->no_hp,
            'password'=>bcrypt($r->password),
            'role'=>'pemilik'
        ]);
        return redirect('/login');
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
