<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // jangan lupa import Log

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek login
        if (!Auth::guard('web')->check()) {
            Log::info('AdminMiddleware: user not logged in');
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();

        // Log info untuk debugging
        Log::info('AdminMiddleware:', [
            'check' => Auth::guard('web')->check(),
            'role_id' => $user->role_id
        ]);

        // Cek role admin
        if ((int) $user->role_id !== 1) {
            Log::info('AdminMiddleware: access denied for non-admin');
            abort(403);
        }

        return $next($request);
    }
}
