<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login via REST API untuk Petugas Apel (Aplikasi Android / Client)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input data tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim($request->input('identifier'));
        $password = $request->input('password');

        // Cari berdasarkan username atau email
        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        // FR-01: Pesan kesalahan aman, tidak membocorkan eksistensi akun
        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial yang dimasukkan tidak cocok.',
            ], 401);
        }

        // Cek apakah akun aktif
        if (! $user->isActive()) {
            return response()->json([
                'success' => false,
                'code' => 'ACCOUNT_INACTIVE',
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
            ], 403);
        }

        // Update waktu login terakhir
        $user->update(['last_login_at' => now()]);

        // Buat Sanctum token
        $deviceName = $request->input('device_name', 'Android-Client');
        $token = $user->createToken($deviceName)->plainTextToken;

        AuditLog::log(
            action: 'API_LOGIN',
            description: "Pengguna {$user->name} ({$user->role}) berhasil login via API perangkat {$deviceName}.",
            userId: $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Mendapatkan profil pengguna aktif
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->isActive(),
            ],
        ]);
    }

    /**
     * Logout dan cabut token aktif
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        AuditLog::log(
            action: 'API_LOGOUT',
            description: "Pengguna {$user?->name} logout dari API.",
            userId: $user?->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dan token dinonaktifkan.',
        ]);
    }
}
