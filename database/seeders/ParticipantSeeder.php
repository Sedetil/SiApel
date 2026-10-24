<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\RfidCard;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            [
                'identifier' => '22753001',
                'name' => 'Ahmad Fauzi',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'Kompi A - Pleton 1',
                'gender' => 'L',
                'phone' => '081234567801',
                'card_uid' => 'E2000019',
            ],
            [
                'identifier' => '22753002',
                'name' => 'Annisa Permata Putri',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'Kompi A - Pleton 1',
                'gender' => 'P',
                'phone' => '081234567802',
                'card_uid' => 'E2000020',
            ],
            [
                'identifier' => '22753003',
                'name' => 'Bayu Pratama',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'Kompi A - Pleton 2',
                'gender' => 'L',
                'phone' => '081234567803',
                'card_uid' => 'E2000021',
            ],
            [
                'identifier' => '22753004',
                'name' => 'Citra Dewi Lestari',
                'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                'class_group' => 'Kompi A - Pleton 2',
                'gender' => 'P',
                'phone' => '081234567804',
                'card_uid' => 'E2000022',
            ],
            [
                'identifier' => '22753005',
                'name' => 'Dimas Arya Putra',
                'study_program' => 'Teknologi Rekayasa Internet',
                'class_group' => 'Kompi B - Pleton 1',
                'gender' => 'L',
                'phone' => '081234567805',
                'card_uid' => 'E2000023',
            ],
            [
                'identifier' => '22753006',
                'name' => 'Eka Wahyuni',
                'study_program' => 'Teknologi Rekayasa Internet',
                'class_group' => 'Kompi B - Pleton 1',
                'gender' => 'P',
                'phone' => '081234567806',
                'card_uid' => 'E2000024',
            ],
            [
                'identifier' => '22753007',
                'name' => 'Fajar Hidayat',
                'study_program' => 'Agribisnis Pangan',
                'class_group' => 'Kompi B - Pleton 2',
                'gender' => 'L',
                'phone' => '081234567807',
                'card_uid' => 'E2000025',
            ],
            [
                'identifier' => '22753008',
                'name' => 'Gita Gutawa Putri',
                'study_program' => 'Akuntansi Perpajakan',
                'class_group' => 'Kompi B - Pleton 2',
                'gender' => 'P',
                'phone' => '081234567808',
                'card_uid' => 'E2000026',
            ],
            [
                'identifier' => '22753009',
                'name' => 'Hendra Setiawan (Nonaktif)',
                'study_program' => 'Manajemen Informatika',
                'class_group' => 'Kompi A - Pleton 1',
                'gender' => 'L',
                'phone' => '081234567809',
                'card_uid' => null,
                'is_active' => false,
            ],
        ];

        foreach ($participants as $p) {
            $cardUid = $p['card_uid'] ?? null;
            $isActive = $p['is_active'] ?? true;
            unset($p['card_uid'], $p['is_active']);

            $participant = Participant::updateOrCreate(
                ['identifier' => $p['identifier']],
                array_merge($p, ['is_active' => $isActive])
            );

            if ($cardUid) {
                RfidCard::updateOrCreate(
                    ['participant_id' => $participant->id, 'card_uid' => $cardUid],
                    [
                        'is_active' => true,
                        'issued_at' => now(),
                        'notes' => 'Penerbitan kartu perdana mahasiswa',
                    ]
                );
            }
        }
    }
}
