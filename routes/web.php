<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Route::get('/_debug-auth', function () {
    return response()->json([
        'auth_check' => Auth::check(),
        'user' => Auth::user(),
        'user_id' => Auth::id(),
        'role_id' => Auth::user()?->role_id,
        'session_id' => session()->getId(),
        'session_all' => Session::all(),
    ]);
});