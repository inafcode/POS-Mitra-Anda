<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials =$request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {$request->session()->regenerate();

            // Jika yang login adalah instansi, arahkan ke dashboard portal instansi
            if (Auth::user()->role === 'instansi') {
                return redirect('/portal-instansi');
            }

            // Jika Admin / Kasir, masuk ke gudang
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah!',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}