<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfficerController extends Controller
{
    public function index(Request $request): View
    {
        $officers = User::whereIn('role', ['petugas', 'timdis'])
            ->orderBy('name', 'asc')
            ->paginate(15);

        $departments = Department::with(['studyPrograms.classes'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.officers.index', compact('officers', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:pengguna,username',
            'email' => 'required|email|max:255|unique:pengguna,email',
            'password' => 'required|string|min:6',
            'department' => 'nullable|string|max:150',
            'study_program' => 'nullable|string|max:150',
            'class_group' => 'nullable',
            'class_groups' => 'nullable|array',
            'cohort_year' => 'nullable|string|max:100',
            'role' => 'nullable|string|in:timdis,petugas',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $role = $validated['role'] ?? 'timdis';

        $classGroupFinal = null;
        if (! empty($validated['class_groups']) && is_array($validated['class_groups'])) {
            $classGroupFinal = implode(', ', array_unique(array_filter(array_map('trim', $validated['class_groups']))));
        } elseif (! empty($validated['class_group'])) {
            $classGroupFinal = is_array($validated['class_group'])
                ? implode(', ', array_unique(array_filter(array_map('trim', $validated['class_group']))))
                : trim($validated['class_group']);
        }

        $officer = User::create([
            'name' => $validated['name'],
            'username' => trim($validated['username']),
            'email' => trim($validated['email']),
            'department' => $validated['department'] ?? null,
            'study_program' => $validated['study_program'] ?? null,
            'class_group' => $classGroupFinal,
            'cohort_year' => $validated['cohort_year'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'is_active' => true,
        ]);

        AuditLog::log(
            action: 'CREATE_OFFICER',
            description: "Membuat akun {$role} baru: {$officer->name} ({$officer->username}).",
            entityType: 'User',
            entityId: $officer->id
        );

        return redirect()->route('admin.officers.index')->with('status', "Akun {$role} {$officer->name} berhasil dibuat.");
    }

    public function update(Request $request, User $officer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique($officer->getTable(), 'username')->ignore($officer->getKey(), $officer->getKeyName())],
            'email' => ['required', 'email', 'max:255', Rule::unique($officer->getTable(), 'email')->ignore($officer->getKey(), $officer->getKeyName())],
            'department' => 'nullable|string|max:150',
            'study_program' => 'nullable|string|max:150',
            'class_group' => 'nullable',
            'class_groups' => 'nullable|array',
            'cohort_year' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        $classGroupFinal = null;
        if (! empty($validated['class_groups']) && is_array($validated['class_groups'])) {
            $classGroupFinal = implode(', ', array_unique(array_filter(array_map('trim', $validated['class_groups']))));
        } elseif (! empty($validated['class_group'])) {
            $classGroupFinal = is_array($validated['class_group'])
                ? implode(', ', array_unique(array_filter(array_map('trim', $validated['class_group']))))
                : trim($validated['class_group']);
        }

        $updateData = [
            'name' => $validated['name'],
            'username' => trim($validated['username']),
            'email' => trim($validated['email']),
            'department' => $validated['department'] ?? null,
            'study_program' => $validated['study_program'] ?? null,
            'class_group' => $classGroupFinal,
            'cohort_year' => $validated['cohort_year'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $officer->update($updateData);

        AuditLog::log(
            action: 'UPDATE_OFFICER',
            description: "Memperbarui akun timdis: {$officer->name}.",
            entityType: 'User',
            entityId: $officer->id
        );

        return redirect()->route('admin.officers.index')->with('status', "Data akun timdis {$officer->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $officer): RedirectResponse
    {
        $newStatus = ! $officer->is_active;
        $officer->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::log(
            action: 'TOGGLE_OFFICER_STATUS',
            description: "Mengubah status akun timdis {$officer->name} menjadi {$statusText}.",
            entityType: 'User',
            entityId: $officer->id
        );

        return back()->with('status', "Akun timdis {$officer->name} berhasil {$statusText}.");
    }

    public function destroy(User $officer): RedirectResponse
    {
        $name = $officer->name;
        $id = $officer->id;

        $officer->delete();

        AuditLog::log(
            action: 'DELETE_OFFICER',
            description: "Menghapus akun timdis: {$name}.",
            entityType: 'User',
            entityId: $id
        );

        return redirect()->route('admin.officers.index')->with('status', "Akun timdis {$name} berhasil dihapus.");
    }
}
