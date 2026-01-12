<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // CAST KE INT (INI KUNCINYA)
        if ((int) $user->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
