<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the HR Administrator login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming HR authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->isCandidate()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akses ditolak: Akun Anda adalah akun Pelamar/Kandidat. Silakan masuk melalui Portal Karir.',
            ])->with('redirect_portal_url', route('career.login'))
                ->with('redirect_portal_label', 'Buka Portal Pelamar')
                ->onlyInput('email');
        }

        if (! $user->isHr()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akses ditolak: Akun Anda adalah akun Karyawan (bukan Administrator HR). Silakan masuk melalui Portal Karyawan.',
            ])->with('redirect_portal_url', route('employee.login'))
                ->with('redirect_portal_label', 'Buka Portal Karyawan')
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('portal'))
            ->with('success', 'Selamat datang kembali, '.$user->name.'!');
    }

    /**
     * Display the Employee (ESS) login view.
     */
    public function createEmployee(): View
    {
        return view('auth.employee-login');
    }

    /**
     * Handle an incoming Employee authentication request.
     */
    public function storeEmployee(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->isCandidate()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akses ditolak: Akun Anda adalah akun Pelamar/Kandidat. Silakan masuk melalui Portal Karir.',
            ])->with('redirect_portal_url', route('career.login'))
                ->with('redirect_portal_label', 'Buka Portal Pelamar')
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('employee.dashboard'))
            ->with('success', 'Selamat datang di Portal Karyawan, '.$user->name.'!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isCandidate = $user?->isCandidate();
        $isEmployeeOnly = $user && ! $user->isHr() && ! $user->isCandidate();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($isCandidate) {
            return redirect()->route('career.login')
                ->with('success', 'Anda telah berhasil keluar dari Portal Pelamar.');
        }

        if ($isEmployeeOnly) {
            return redirect()->route('employee.login')
                ->with('success', 'Anda telah berhasil keluar dari Portal Karyawan.');
        }

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem HRIS.');
    }
}
