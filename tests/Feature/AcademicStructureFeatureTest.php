<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\Department;
use App\Models\Participant;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStructureFeatureTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;
    protected User $timdis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name' => 'Admin Test',
                'email' => 'admin_test@polinela.ac.id',
                'role' => 'admin',
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]
        );

        $this->timdis = User::firstOrCreate(
            ['username' => 'timdis_test'],
            [
                'name' => 'Timdis Test',
                'email' => 'timdis_test@polinela.ac.id',
                'role' => 'timdis',
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_access_academic_structure_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('academic-structures.index'));
        $response->assertStatus(200);
        $response->assertSee('Struktur Akademik & Kelas', false);
    }

    public function test_timdis_cannot_access_academic_structure_page(): void
    {
        $response = $this->actingAs($this->timdis)->get(route('academic-structures.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_create_department_study_program_and_class(): void
    {
        // 1. Tambah Department
        $resDept = $this->actingAs($this->admin)->post(route('academic-structures.departments.store'), [
            'name' => 'Jurusan Teknik Otomotif',
            'code' => 'JTO',
        ]);
        $resDept->assertSessionHasNoErrors();
        $dept = Department::where('code', 'JTO')->first();
        $this->assertNotNull($dept);

        // 2. Tambah Study Program
        $resProdi = $this->actingAs($this->admin)->post(route('academic-structures.study-programs.store'), [
            'department_id' => $dept->id,
            'name' => 'Teknik Mesin Otomotif',
            'degree_level' => 'Sarjana Terapan',
            'code' => 'TMO',
        ]);
        $resProdi->assertSessionHasNoErrors();
        $prodi = StudyProgram::where('code', 'TMO')->first();
        $this->assertNotNull($prodi);

        // 3. Tambah Class
        $resClass = $this->actingAs($this->admin)->post(route('academic-structures.classes.store'), [
            'study_program_id' => $prodi->id,
            'name' => 'TMO 1A',
            'cohort_year' => '2025',
        ]);
        $resClass->assertSessionHasNoErrors();
        $class = AcademicClass::where('name', 'TMO 1A')->first();
        $this->assertNotNull($class);
    }

    public function test_admin_can_update_department_study_program_and_class(): void
    {
        $dept = Department::create(['name' => 'Jurusan Peternakan', 'code' => 'JPT']);
        $prodi = StudyProgram::create([
            'department_id' => $dept->id,
            'name' => 'Produksi Ternak',
            'degree_level' => 'D3',
            'code' => 'PT',
        ]);
        $class = AcademicClass::create([
            'study_program_id' => $prodi->id,
            'name' => 'PT 1A',
            'cohort_year' => '2024',
        ]);

        // 1. Update Dept
        $resDept = $this->actingAs($this->admin)->put(route('academic-structures.departments.update', $dept), [
            'name' => 'Jurusan Peternakan Unggul',
            'code' => 'JPTU',
        ]);
        $resDept->assertSessionHasNoErrors();
        $this->assertEquals('Jurusan Peternakan Unggul', $dept->fresh()->name);

        // 2. Update Prodi
        $resProdi = $this->actingAs($this->admin)->put(route('academic-structures.study-programs.update', $prodi), [
            'name' => 'Teknologi Produksi Ternak',
            'degree_level' => 'Sarjana Terapan',
            'code' => 'TPT',
        ]);
        $resProdi->assertSessionHasNoErrors();
        $this->assertEquals('Teknologi Produksi Ternak', $prodi->fresh()->name);

        // 3. Update Class
        $resClass = $this->actingAs($this->admin)->put(route('academic-structures.classes.update', $class), [
            'name' => 'TPT 1B',
            'cohort_year' => '2025',
        ]);
        $resClass->assertSessionHasNoErrors();
        $this->assertEquals('TPT 1B', $class->fresh()->name);
        $this->assertEquals('2025', $class->fresh()->cohort_year);
    }

    public function test_admin_can_open_participant_create_and_edit_with_academic_dropdown(): void
    {
        $resCreate = $this->actingAs($this->admin)->get(route('participants.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Struktur Akademik Mahasiswa');

        $participant = Participant::firstOrCreate(
            ['identifier' => 'TEST_NPM_001'],
            [
                'name' => 'Mahasiswa Test',
                'department' => 'Teknologi Informasi',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'TRPL 1A',
                'cohort_year' => '2025',
                'is_active' => true,
            ]
        );

        $resEdit = $this->actingAs($this->admin)->get(route('participants.edit', $participant));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Ubah Data Peserta');
        $resEdit->assertSee('Struktur Akademik');
    }

    public function test_admin_can_open_officers_management(): void
    {
        $res = $this->actingAs($this->admin)->get(route('officers.index'));
        $res->assertStatus(200);
        $res->assertSee('Kelola Akun Timdis');
        $res->assertSee('Penugasan Wilayah Binaan Apel');
    }
}
