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
            if (Auth::user()->isAdmin()) {
                // Successful Login Admin
                return redirect()->intended(route('dashboard'))->with('notify', [
                    'type' => 'success',
                    'title' => __('auth.notify_welcome_title'),
                    'message' => __('auth.notify_welcome_message'),
                ]);
            } elseif (Auth::user()->isDealer()) {
                // Successful Login Dealer
                return redirect()->intended(route('dealer.marketplace.index'))->with('notify', [
                    'type' => 'success',
                    'title' => __('auth.notify_welcome_title'),
                    'message' => __('auth.notify_welcome_message'),
                ]);
            } else {
                Auth::logout();

                return back()->with('notify', [
                    'type' => 'warning',
                    'title' => __('auth.notify_access_denied_title'),
                    'message' => __('auth.notify_access_denied_message'),
                ])->onlyInput('email');
            }
        }

        // Failed Login
        return back()->with('notify', [
            'type' => 'error',
            'title' => __('auth.notify_auth_failed_title'),
            'message' => __('auth.notify_auth_failed_message'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('notify', [
            'type' => 'info',
            'title' => __('auth.notify_signed_out_title'),
            'message' => __('auth.notify_signed_out_message'),
        ]);
    }
}
