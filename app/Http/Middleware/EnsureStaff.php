<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('staff.login');
        }

        if (!Auth::user()->isStaff()) {
            Auth::logout();
            return redirect()->route('staff.login')->withErrors(['login' => 'Akses ditolak: Hanya untuk akun Staf Pelayanan.']);
        }

        return $next($request);
    }
}
