<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicStructureController extends Controller
{
    public function index(): View
    {
        $departments = Department::with(['studyPrograms.classes'])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_departments' => Department::count(),
            'total_programs' => StudyProgram::count(),
            'total_classes' => AcademicClass::count(),
        ];

        return view('pages.academic-structures.index', compact('departments', 'stats'));
    }

    // --- Department Actions ---
    public function storeDepartment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:departments,name',
            'code' => 'nullable|string|max:50|unique:departments,code',
        ]);

        $dept = Department::create($validated);

        AuditLog::log(
            action: 'CREATE_DEPARTMENT',
            description: "Menambahkan Jurusan baru: {$dept->name}",
            entityType: 'Department',
            entityId: $dept->id
        );

        return back()->with('status', "Jurusan '{$dept->name}' berhasil ditambahkan.");
    }

    public function updateDepartment(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->ignore($department->id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('departments', 'code')->ignore($department->id)],
        ]);

        $oldName = $department->name;
        $department->update($validated);

        AuditLog::log(
            action: 'UPDATE_DEPARTMENT',
            description: "Memperbarui data Jurusan: {$oldName} -> {$department->name}",
            entityType: 'Department',
            entityId: $department->id
        );

        return back()->with('status', "Jurusan '{$department->name}' berhasil diperbarui.");
    }

    public function destroyDepartment(Department $department): RedirectResponse
    {
        $name = $department->name;
        $department->delete();

        AuditLog::log(
            action: 'DELETE_DEPARTMENT',
            description: "Menghapus Jurusan: {$name}",
            entityType: 'Department'
        );

        return back()->with('status', "Jurusan '{$name}' dan program studi terkait berhasil dihapus.");
    }

    // --- Study Program Actions ---
    public function storeStudyProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'degree_level' => 'required|string|max:20',
        ]);

        $prodi = StudyProgram::create($validated);

        AuditLog::log(
            action: 'CREATE_STUDY_PROGRAM',
            description: "Menambahkan Program Studi: {$prodi->name}",
            entityType: 'StudyProgram',
            entityId: $prodi->id
        );

        return back()->with('status', "Program Studi '{$prodi->name}' berhasil ditambahkan.");
    }

    public function updateStudyProgram(Request $request, StudyProgram $studyProgram): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'degree_level' => 'required|string|max:20',
        ]);

        $oldName = $studyProgram->name;
        $studyProgram->update($validated);

        AuditLog::log(
            action: 'UPDATE_STUDY_PROGRAM',
            description: "Memperbarui data Program Studi: {$oldName} -> {$studyProgram->name}",
            entityType: 'StudyProgram',
            entityId: $studyProgram->id
        );

        return back()->with('status', "Program Studi '{$studyProgram->name}' berhasil diperbarui.");
    }

    public function destroyStudyProgram(StudyProgram $studyProgram): RedirectResponse
    {
        $name = $studyProgram->name;
        $studyProgram->delete();

        AuditLog::log(
            action: 'DELETE_STUDY_PROGRAM',
            description: "Menghapus Program Studi: {$name}",
            entityType: 'StudyProgram'
        );

        return back()->with('status', "Program Studi '{$name}' berhasil dihapus.");
    }

    // --- Class Actions ---
    public function storeClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'name' => 'required|string|max:50',
            'cohort_year' => 'required|string|max:10',
        ]);

        $cls = AcademicClass::create($validated);

        AuditLog::log(
            action: 'CREATE_ACADEMIC_CLASS',
            description: "Menambahkan Kelas: {$cls->name} (Angkatan {$cls->cohort_year})",
            entityType: 'AcademicClass',
            entityId: $cls->id
        );

        return back()->with('status', "Kelas '{$cls->name}' (Angkatan {$cls->cohort_year}) berhasil ditambahkan.");
    }

    public function updateClass(Request $request, AcademicClass $academicClass): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'cohort_year' => 'required|string|max:10',
        ]);

        $oldName = $academicClass->name;
        $academicClass->update($validated);

        AuditLog::log(
            action: 'UPDATE_ACADEMIC_CLASS',
            description: "Memperbarui data Kelas: {$oldName} -> {$academicClass->name} (Angkatan {$academicClass->cohort_year})",
            entityType: 'AcademicClass',
            entityId: $academicClass->id
        );

        return back()->with('status', "Kelas '{$academicClass->name}' berhasil diperbarui.");
    }

    public function destroyClass(AcademicClass $academicClass): RedirectResponse
    {
        $name = $academicClass->name;
        $academicClass->delete();

        AuditLog::log(
            action: 'DELETE_ACADEMIC_CLASS',
            description: "Menghapus Kelas: {$name}",
            entityType: 'AcademicClass'
        );

        return back()->with('status', "Kelas '{$name}' berhasil dihapus.");
    }
}
