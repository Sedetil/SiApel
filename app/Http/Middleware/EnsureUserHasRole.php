<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        // Cek status aktif akun
        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ACCOUNT_INACTIVE',
                    'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'identifier' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        // Cek kecocokan peran
        if (! empty($roles) && ! $user->hasRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'FORBIDDEN',
                    'message' => 'Anda tidak memiliki hak akses untuk tindakan ini.',
                ], 403);
            }

            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
