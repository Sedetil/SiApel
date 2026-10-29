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

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($user->id, 'id_pengguna')],
            'phone' => 'nullable|string|max:30',
        ];

        if ($request->filled('username')) {
            $rules['username'] = ['required', 'string', 'max:100', Rule::unique('pengguna', 'username')->ignore($user->id, 'id_pengguna')];
        }

        // Jika mengisi kata sandi baru atau kata sandi saat ini
        if ($request->filled('password') || $request->filled('current_password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi jika ingin mengubah kata sandi.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (($request->filled('password') || $request->filled('current_password')) && ! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withInput()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
        }

        $updateData = [
            'name' => $validated['name'],
        ];

        if ($request->filled('email')) {
            $updateData['email'] = $validated['email'];
        }
        if ($request->has('phone')) {
            $updateData['phone'] = $validated['phone'];
        }
        if ($request->filled('username')) {
            $updateData['username'] = $validated['username'];
        }
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLog::log(
            action: 'UPDATE_PROFILE',
            description: "Memperbarui data profil akun: {$user->name}.",
            entityType: 'User',
            entityId: $user->id
        );

        return redirect()->route(role_route_name('profile.edit'))->with('status', 'Perubahan profil berhasil disimpan.');
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

        return redirect()->route(role_route_name('profile.edit'))->with('password_status', 'Kata sandi berhasil diubah.');
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

        return redirect()->route(role_route_name('profile.edit'))->with('avatar_status', 'Foto profil berhasil diperbarui.');
    }
}
