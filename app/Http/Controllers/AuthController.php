<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->role_id === 1
                ? redirect()->route('admin.index')
                : redirect()->route('pegawai.index');
        }

        return view('login', [
            'title' => 'Login'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('message', 'Email tidak terdaftar');
        }

        if ($user->is_active !== 1) {
            return back()->with('message', 'Email belum diaktivasi');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('message', 'Password salah');
        }

        // 🔐 LOGIN YANG BENAR
        Auth::guard('web')->login($user);

        // 🔄 WAJIB
        $request->session()->regenerate();

        return $user->role_id === 1
            ? redirect()->route('admin.index')
            : redirect()->route('pegawai.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'Anda berhasil logout');
    }
}
