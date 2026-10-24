<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('pages.profile.edit', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'study_program' => 'nullable|string|max:150',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
        ]);

        $user->update($validated);

        AuditLog::log(
            action: 'UPDATE_PROFILE',
            description: "Memperbarui data profil akun: {$user->name}.",
            entityType: 'User',
            entityId: $user->id
        );

        return redirect()->route('profile.edit')->with('status', 'Data profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.min' => 'Kata sandi baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::log(
            action: 'CHANGE_PASSWORD',
            description: "Mengubah kata sandi akun: {$user->name}.",
            entityType: 'User',
            entityId: $user->id
        );

        return redirect()->route('profile.edit')->with('password_status', 'Kata sandi berhasil diubah.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.required' => 'Silakan pilih berkas foto.',
            'avatar.image' => 'Berkas harus berupa gambar.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        AuditLog::log(
            action: 'UPDATE_AVATAR',
            description: "Memperbarui foto profil akun: {$user->name}.",
            entityType: 'User',
            entityId: $user->id
        );

        return redirect()->route('profile.edit')->with('avatar_status', 'Foto profil berhasil diperbarui.');
    }
}
