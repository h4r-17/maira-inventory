<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // mastiin user dah login, kalo belum login redirect ke halaman login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = $request->user()->role?->nama_role;

        // cek apakah user role ada di roles yang diizinkan
        if ($userRole === 'Super Admin' || in_array($userRole, $roles))
            return $next($request);

        abort(403, 'Akses ditolak.');
    }
}
