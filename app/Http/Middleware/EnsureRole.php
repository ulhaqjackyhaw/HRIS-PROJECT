<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request based on user role.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return match ($role) {
                'candidate' => redirect()->route('career.login'),
                'employee' => redirect()->route('employee.login'),
                default => redirect()->route('login'),
            };
        }

        if ($role === 'hr') {
            if (! $user->isHr()) {
                if ($user->isCandidate()) {
                    return redirect()->route('career.dashboard')
                        ->with('error', 'Akses ditolak: Akun Anda tidak memiliki wewenang Administrator HR.');
                }

                return redirect()->route('employee.dashboard')
                    ->with('error', 'Akses ditolak: Halaman ini dikhususkan untuk Administrator HR.');
            }
        }

        if ($role === 'employee') {
            if (! $user->isInternal()) {
                return redirect()->route('career.dashboard')
                    ->with('error', 'Akses ditolak: Halaman ini hanya untuk karyawan internal.');
            }
        }

        if ($role === 'candidate') {
            if (! $user->isCandidate()) {
                if ($user->isHr()) {
                    return redirect()->route('portal')
                        ->with('info', 'Anda telah masuk sebagai Administrator HR.');
                }

                return redirect()->route('employee.dashboard')
                    ->with('info', 'Anda telah masuk sebagai Karyawan internal.');
            }
        }

        return $next($request);
    }
}
