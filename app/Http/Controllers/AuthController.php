<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->role_id == 1
                ? redirect()->route('admin.index')
                : redirect()->route('pegawai.index');
        }

        return view('login', [
            'title' => 'Login'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->with('message', 'Email atau password salah');
        }

        // WAJIB
        $request->session()->regenerate();

        return Auth::user()->role_id == 1
            ? redirect()->route('admin.index')
            : redirect()->route('pegawai.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'Anda berhasil logout');
    }
}
