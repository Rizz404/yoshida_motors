<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba Login
        // attempt() otomatis hash password input & bandingin sama di DB
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Cek Role: Cuma Admin yang boleh masuk sini
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Kamu bukan Admin! Hush sana! 😤']);
            }

            return redirect()->intended('dashboard');
        }

        // Kalau gagal
        return back()->withErrors([
            'email' => 'Email atau password salah nih, coba inget-inget lagi! 🤔',
        ])->onlyInput('email');
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
