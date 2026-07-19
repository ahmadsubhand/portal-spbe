<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan pengguna sudah login dan memiliki salah satu role yang sesuai
        if ($request->user() && in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, lempar ke halaman beranda atau abort 403
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}