<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kalau belum login, arahkan ke login (JANGAN abort)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kalau login tapi bukan admin
        if ((int) Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
