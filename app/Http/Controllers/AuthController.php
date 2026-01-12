<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Halaman login
     */
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('login', [
            'title' => 'Login'
        ]);
    }

    /**
     * Proses login
     */
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

        if ($user->is_active != 1) {
            return back()->with('message', 'Akun belum diaktivasi');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('message', 'Password salah');
        }

        // Login user
        Auth::login($user);

        // Penting: regenerate session
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'Anda berhasil logout');
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectByRole(User $user)
    {
        if ((int) $user->role_id === 1) {
            return redirect()->route('admin.index');
        }

        return redirect()->route('pegawai.index');
    }
}
