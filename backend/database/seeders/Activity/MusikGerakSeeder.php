<?php

namespace Database\Seeders\Activity;

use App\ActivityType;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MusikGerakSeeder extends Seeder
{
    public function run(): void
    {
        $dataTables = [
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '"[\\"islam\\"]"',
                'ages' => '[1,2,3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"audio_url":"https:\\/\\/audius.co\\/embed\\/track\\/tonijambo\\/berwudhu?flavor=compact","lyrics":"\\ud83c\\udf88 Cuci dua tangan,\\nlalu kumurlah,\\nbersihkan hidung,\\nlalu mukamu,\\ncuci tangan kanan,\\ncuci tangan kiri,\\nbasuhkan rambut, telinga,\\njuga kaki","moves":["Cuci dua tangan","Masukan air ke hidung","Basuh muka dengan air","Basuh tangan kanan","Basuh tangan kiri","Basuh rambut dan telinga","Basuh kaki"],"moral":"Lagu ini mengajarkan anak berwudhu."}',
                'desc' => 'Lagu anak berwudhu.',
                'image' => NULL,
                'moral' => 'Lagu ini mengajarkan anak berwudhu.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","rutin_belajar"]',
                'slug' => 'berwudhu',
                'sort_order' => 1,
                'status' => 'approved',
                'title' => 'Berwudhu',
                'type' => 'musik_gerak',
                'views' => 0,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[1,2,3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"audio_url":"https:\\/\\/w.soundcloud.com\\/player\\/?url=https%3A\\/\\/api.soundcloud.com\\/tracks\\/soundcloud%253Atracks%253A346703008&color=%23ff5500&auto_play=false&hide_related=true&show_comments=false&show_user=true&show_reposts=false&show_teaser=false","moves":["Dengarkan musik sambil bermain","Bertepuk tangan mengikuti irama","Bernyanyi bersama lagu yang dikenal","Menari bebas sesuai nada"],"moral":"Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan."}',
                'desc' => 'Kumpulan lagu anak Indonesia selama 60 menit untuk menemani belajar dan bermain.',
                'image' => NULL,
                'moral' => 'Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","rutin_belajar"]',
                'slug' => 'lagu-anak-indonesia-60-menit',
                'sort_order' => 2,
                'status' => 'approved',
                'title' => 'Lagu Anak Indonesia 60 Menit',
                'type' => 'musik_gerak',
                'views' => 0,
            ],
            [
                'active' => 1,
                'addon_id' => NULL,
                'agama' => '[]',
                'ages' => '[1,2,3,4,5,6,7,8,9,10,11]',
                'created_by' => 1,
                'creator' => NULL,
                'data' => '{"audio_url":"https:\\/\\/www.youtube.com\\/embed\\/OSFOmU38jZ4","moves":["Dengarkan musik sambil bermain","Bertepuk tangan mengikuti irama","Bernyanyi bersama lagu yang dikenal","Menari bebas sesuai nada"],"moral":"Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan."}',
                'desc' => 'Kumpulan lagu anak Indonesia populer dari YouTube.',
                'image' => NULL,
                'moral' => 'Lagu anak Indonesia mengajarkan nilai-nilai baik dengan cara yang menyenangkan.',
                'notes' => NULL,
                'plans' => NULL,
                'prompt' => NULL,
                'skills' => '["berpikir_kreatif","rutin_belajar"]',
                'slug' => 'lagu-anak-youtube',
                'sort_order' => 3,
                'status' => 'approved',
                'title' => 'Lagu Anak YouTube',
                'type' => 'musik_gerak',
                'views' => 0,
            ],
        ];

        Activity::where('type', ActivityType::MUSIK_GERAK->value)->delete();
        DB::table("activities")->insertOrIgnore($dataTables);
    }
}
