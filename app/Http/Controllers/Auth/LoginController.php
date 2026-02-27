<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Role Authorization Check
            if (Auth::user()->role !== 'admin') {
                Auth::logout();

                return back()->with('notify', [
                    'type' => 'warning',
                    'title' => 'Access Denied',
                    'message' => 'You do not have administrative privileges to access this area.',
                ])->onlyInput('email');
            }

            // Successful Login
            return redirect()->intended(route('dashboard'))->with('notify', [
                'type' => 'success',
                'title' => 'Welcome Back',
                'message' => 'You have successfully logged in.',
            ]);
        }

        // Failed Login
        return back()->with('notify', [
            'type' => 'error',
            'title' => 'Authentication Failed',
            'message' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('notify', [
            'type' => 'info',
            'title' => 'Signed Out',
            'message' => 'You have been logged out successfully.',
        ]);
    }
}
