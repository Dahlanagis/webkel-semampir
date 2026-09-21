<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (!Auth::user()->isAdmin()) {
            if (Auth::user()->isStaff()) {
                return redirect()->route('staff.dashboard')
                    ->with('warning', 'Akses Ditolak: Anda tidak memiliki wewenang Administrator. Anda telah diarahkan ke Panel Staf.');
            }

            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['login' => 'Akses ditolak: Hanya untuk akun Administrator.']);
        }

        return $next($request);
    }
}
