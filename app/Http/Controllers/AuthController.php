<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.index');
            } else {
                return redirect()->route('pegawai.index');
            }
        }

        return view('login', [
            'title' => 'Login'
        ]);
    }


    public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower($request->email)
        ]);
        
        $request->validate([
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.regex'    => 'Email harus menggunakan domain @gmail.com',
            'password.required' => 'Password wajib diisi',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->is_active == 1) {
                if (Hash::check($request->password, $user->password)) {
                    Auth::login($user);

                    if ($user->role_id == 1) {
                        Session::put('masuk_admin', true);
                        return redirect()->route('admin.index');
                    } else {
                        Session::put('masuk_user', true);
                        return redirect()->route('pegawai.index');
                    }
                } else {
                    return back()->with('message', 'Password salah');
                }
            } else {
                return back()->with('message', 'Akun tidak dapat digunakan karena status kepegawaian tidak aktif');
            }
        } else {
            return back()->with('message', 'Email tidak terdaftar');
        }
    }

    public function logout()
    {
        Session::forget(['masuk_admin', 'masuk_user']);
        Auth::logout();
        Session::flash('message', 'Anda berhasil logout');
        return redirect()->route('login');
    }
}
