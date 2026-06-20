<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'addon_id_user' => 1,
                'addon_nama' => 'Kelas Tambahan Berhitung',
                'addon_desc' => 'Paket latihan berhitung tambahan untuk anak usia 4-7 tahun.',
                'addon_harga' => 25000,
                'addon_ages' => [4, 5, 6, 7],
                'addon_agama' => null,
                'addon_bg' => '#E8F5E9',
                'addon_icon' => '🧮',
                'addon_buyers' => [],
                'addon_active' => true,
                'addon_created_at' => now(),
            ],
            [
                'addon_id_user' => 1,
                'addon_nama' => 'Kelas Mewarnai Kreatif',
                'addon_desc' => 'Paket mewarnai untuk anak usia 1-5 tahun.',
                'addon_harga' => 15000,
                'addon_ages' => [1, 2, 3, 4, 5],
                'addon_agama' => null,
                'addon_bg' => '#FFF3E0',
                'addon_icon' => '🖍️',
                'addon_buyers' => [],
                'addon_active' => true,
                'addon_created_at' => now(),
            ],
            [
                'addon_id_user' => 1,
                'addon_nama' => 'Story Telling Islami',
                'addon_desc' => 'Koleksi cerita islami untuk anak.',
                'addon_harga' => 20000,
                'addon_ages' => [3, 4, 5, 6, 7],
                'addon_agama' => ['Islam'],
                'addon_bg' => '#E3F2FD',
                'addon_icon' => '📖',
                'addon_buyers' => [],
                'addon_active' => true,
                'addon_created_at' => now(),
            ],
            [
                'addon_id_user' => 1,
                'addon_nama' => 'Paket Puzzle Otak',
                'addon_desc' => 'Kumpulan puzzle dan latihan otak untuk anak 6-9 tahun.',
                'addon_harga' => 0,
                'addon_ages' => [6, 7, 8, 9],
                'addon_agama' => null,
                'addon_bg' => '#F3E5F5',
                'addon_icon' => '🧠',
                'addon_buyers' => [],
                'addon_active' => true,
                'addon_created_at' => now(),
            ],
        ];

        foreach ($addons as $a) {
            Addon::updateOrCreate(
                ['addon_nama' => $a['addon_nama']],
                $a
            );
        }
    }
}
