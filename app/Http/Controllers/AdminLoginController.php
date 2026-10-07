<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Login failed']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Logs the user out of the application

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to base URL
    }
}
