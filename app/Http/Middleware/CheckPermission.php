<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Middleware untuk proteksi route berdasarkan permission key.
     *
     * Cara pakai di routes/web.php:
     *   ->middleware('permission:view_laporan')
     *
     * Multiple permission (user harus punya SEMUA):
     *   ->middleware('permission:view_laporan,download_laporan')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek setiap permission yang dibutuhkan
        foreach ($permissions as $permission) {
            if (! $request->user()->hasPermission($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Anda tidak memiliki akses untuk tindakan ini.'
                    ], 403);
                }

                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
        }

        return $next($request);
    }
}