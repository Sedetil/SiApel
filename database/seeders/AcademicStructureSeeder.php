<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\Department;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    /**
     * Data Resmi 8 Jurusan dan Program Studi Politeknik Negeri Lampung (POLINELA) 2026.
     */
    public function run(): void
    {
        $structures = [
            // ==========================================
            // 1. JURUSAN TEKNOLOGI INFORMASI (JTI)
            // ==========================================
            [
                'name' => 'Teknologi Informasi',
                'code' => 'JTI',
                'programs' => [
                    [
                        'name' => 'Manajemen Informatika',
                        'code' => 'MI',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'MI 3A', 'cohort_year' => '2024'],
                            ['name' => 'MI 3B', 'cohort_year' => '2024'],
                            ['name' => 'MI 1A', 'cohort_year' => '2025'],
                            ['name' => 'MI 1B', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Rekayasa Perangkat Lunak',
                        'code' => 'TRPL',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRPL 3A', 'cohort_year' => '2024'],
                            ['name' => 'TRPL 1A', 'cohort_year' => '2025'],
                            ['name' => 'TRPL 1B', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Rekayasa Internet',
                        'code' => 'TRI',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRI 3A', 'cohort_year' => '2024'],
                            ['name' => 'TRI 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Rekayasa Elektronika',
                        'code' => 'TRE',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRE 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Sains Data Terapan',
                        'code' => 'SDT',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'SDT 1A', 'cohort_year' => '2026'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 2. JURUSAN EKONOMI DAN BISNIS (JEB)
            // ==========================================
            [
                'name' => 'Ekonomi dan Bisnis',
                'code' => 'JEB',
                'programs' => [
                    [
                        'name' => 'Akuntansi Perpajakan',
                        'code' => 'AKT',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'AKT 4A', 'cohort_year' => '2023'],
                            ['name' => 'AKT 4B', 'cohort_year' => '2023'],
                            ['name' => 'AKT 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Akuntansi Bisnis Digital',
                        'code' => 'ABD',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'ABD 2A', 'cohort_year' => '2024'],
                            ['name' => 'ABD 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Agribisnis Pangan',
                        'code' => 'AGB',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'AGB 4A', 'cohort_year' => '2023'],
                            ['name' => 'AGB 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Pengelolaan Agribisnis',
                        'code' => 'PAB',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PAB 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Perjalanan Wisata',
                        'code' => 'PW',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'PW 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Pengelolaan Perhotelan',
                        'code' => 'PPH',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PPH 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Pengelolaan Konvensi dan Acara',
                        'code' => 'PKA',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PKA 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Produksi Media',
                        'code' => 'PM',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PM 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 3. JURUSAN TEKNIK (JT)
            // ==========================================
            [
                'name' => 'Teknik',
                'code' => 'JT',
                'programs' => [
                    [
                        'name' => 'Teknologi Rekayasa Konstruksi Jalan dan Jembatan',
                        'code' => 'TRKJJ',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRKJJ 3A', 'cohort_year' => '2024'],
                            ['name' => 'TRKJJ 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Rekayasa Otomotif',
                        'code' => 'TRO',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRO 2A', 'cohort_year' => '2024'],
                            ['name' => 'TRO 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Rekayasa Kimia Industri',
                        'code' => 'TRKI',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TRKI 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknik Sumberdaya Lahan dan Lingkungan',
                        'code' => 'TSLL',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'TSLL 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 4. JURUSAN TEKNOLOGI PERTANIAN (JTP)
            // ==========================================
            [
                'name' => 'Teknologi Pertanian',
                'code' => 'JTP',
                'programs' => [
                    [
                        'name' => 'Teknologi Pangan',
                        'code' => 'TP',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'TP 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Mekanisasi Pertanian',
                        'code' => 'MP',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'MP 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Pengembangan Produk Agroindustri',
                        'code' => 'PPA',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PPA 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Kimia Terapan',
                        'code' => 'KT',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'KT 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Gizi Klinis',
                        'code' => 'GK',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'GK 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                    [
                        'name' => 'Pengolahan Patiseri',
                        'code' => 'PAT',
                        'degree_level' => 'D2 Fast Track',
                        'classes' => [
                            ['name' => 'PAT 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 5. JURUSAN BUDIDAYA TANAMAN PANGAN (JBTP)
            // ==========================================
            [
                'name' => 'Budidaya Tanaman Pangan',
                'code' => 'JBTP',
                'programs' => [
                    [
                        'name' => 'Teknologi Produksi Tanaman Pangan',
                        'code' => 'TPTP',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TPTP 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Hortikultura',
                        'code' => 'HORTI',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'HORTI 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Perbenihan',
                        'code' => 'BENIH',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'BENIH 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Produksi Tanaman Hortikultura',
                        'code' => 'TPTH',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TPTH 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 6. JURUSAN BUDIDAYA TANAMAN PERKEBUNAN (JBTK)
            // ==========================================
            [
                'name' => 'Budidaya Tanaman Perkebunan',
                'code' => 'JBTK',
                'programs' => [
                    [
                        'name' => 'Produksi dan Manajemen Industri Perkebunan',
                        'code' => 'PMIP',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PMIP 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Produksi Tanaman Perkebunan',
                        'code' => 'PTP',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'PTP 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Pengelolaan Perkebunan Kopi',
                        'code' => 'KOPI',
                        'degree_level' => 'D2 Fast Track',
                        'classes' => [
                            ['name' => 'KOPI 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 7. JURUSAN PETERNAKAN (JP)
            // ==========================================
            [
                'name' => 'Peternakan',
                'code' => 'JP',
                'programs' => [
                    [
                        'name' => 'Teknologi Produksi Ternak',
                        'code' => 'TPT',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TPT 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Budidaya Peternakan',
                        'code' => 'BPT',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'BPT 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Pakan Ternak',
                        'code' => 'PAKAN',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'PAKAN 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 8. JURUSAN PERIKANAN DAN KELAUTAN (JPK)
            // ==========================================
            [
                'name' => 'Perikanan dan Kelautan',
                'code' => 'JPK',
                'programs' => [
                    [
                        'name' => 'Budidaya Perikanan',
                        'code' => 'BDP',
                        'degree_level' => 'D3',
                        'classes' => [
                            ['name' => 'BDP 3A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Pembenihan Ikan',
                        'code' => 'TPI',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TPI 2A', 'cohort_year' => '2024'],
                        ],
                    ],
                    [
                        'name' => 'Teknologi Akuakultur',
                        'code' => 'TAK',
                        'degree_level' => 'Sarjana Terapan',
                        'classes' => [
                            ['name' => 'TAK 1A', 'cohort_year' => '2025'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($structures as $deptData) {
            $dept = Department::updateOrCreate(
                ['name' => $deptData['name']],
                ['code' => $deptData['code'], 'is_active' => true]
            );

            foreach ($deptData['programs'] as $progData) {
                $prodi = StudyProgram::updateOrCreate(
                    [
                        'department_id' => $dept->id,
                        'name' => $progData['name'],
                    ],
                    [
                        'code' => $progData['code'],
                        'degree_level' => $progData['degree_level'],
                        'is_active' => true,
                    ]
                );

                if (isset($progData['classes'])) {
                    foreach ($progData['classes'] as $classData) {
                        AcademicClass::updateOrCreate(
                            [
                                'study_program_id' => $prodi->id,
                                'name' => $classData['name'],
                            ],
                            [
                                'cohort_year' => $classData['cohort_year'],
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        }
    }
}
