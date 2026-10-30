<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('timdis.dashboard');
        }

        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'NPM, NIP, Username, atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = trim($credentials['identifier']);
        $password = $credentials['password'];

        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        // FR-01: Pesan aman, tidak membocorkan eksistensi akun
        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withInput($request->only('identifier'))->withErrors([
                'identifier' => 'Kombinasi kredensial login tidak sesuai dengan data terdaftar.',
            ]);
        }

        // FR-01: Akun nonaktif tidak boleh memulai sesi
        if (! $user->isActive()) {
            return back()->withInput($request->only('identifier'))->withErrors([
                'identifier' => 'Akun Anda dinonaktifkan oleh Administrator. Hubungi pihak pengelola.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        AuditLog::log(
            action: 'WEB_LOGIN',
            description: "Pengguna {$user->name} ({$user->role}) berhasil masuk ke dashboard web.",
            userId: $user->id
        );

        $targetRoute = $user->isAdmin() ? route('admin.dashboard') : route('timdis.dashboard');

        return redirect()->intended($targetRoute)->with('status', "Selamat datang kembali, {$user->name}.");
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::log(
                action: 'WEB_LOGOUT',
                description: "Pengguna {$user->name} keluar dari dashboard web.",
                userId: $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
