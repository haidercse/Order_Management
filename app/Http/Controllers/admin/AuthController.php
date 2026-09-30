<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('backend.pages.auth.login');
    }

    public function loginAll(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        $remember = $request->filled('remember');
        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withErrors(['Invalid credentials']);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
