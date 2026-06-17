<?php

return [

    'types' => [

        'storytelling' => [
            'label'          => 'Story Telling',
            'emoji'          => '📖',
            'service'        => \App\Services\StoryGeneratorService::class,
            'argument'       => 'theme',
            'argument_label' => 'Story theme',
            'argument_hint'  => 'e.g. kebersamaan, kejujuran, kemandirian, kisah_nabi',
            'default_pages'  => 16,
            'options'        => [
                'child' => 'Child name (auto-generated if empty)',
                'pages' => 'Number of pages (default 16)',
                'ages'  => 'Target ages (e.g. 7 or 3,4,5,6,7,8)',
                'agama' => 'Religion tag (e.g. islam, kristen, katholik, hindu, budha)',
            ],
        ],

        'komik' => [
            'label'          => 'Komik Anak',
            'emoji'          => '💬',
            'service'        => \App\Services\ComicGeneratorService::class,
            'argument'       => 'theme',
            'argument_label' => 'Comic theme',
            'argument_hint'  => 'e.g. petualangan, persahabatan, kejujuran, kemandirian',
            'default_pages'  => 16,
            'options'        => [
                'child' => 'Child name (auto-generated if empty)',
                'pages' => 'Number of panels (default 16)',
                'ages'  => 'Target ages (e.g. 7 or 3,4,5,6,7,8)',
                'agama' => 'Religion tag (e.g. islam, kristen, katholik, hindu, budha)',
            ],
        ],

        'coloring' => [
            'label'          => 'Coloring',
            'emoji'          => '🖍️',
            'service'        => \App\Services\ColoringGeneratorService::class,
            'argument'       => 'theme',
            'argument_label' => 'Coloring subject',
            'argument_hint'  => 'e.g. hewan, buah, kendaraan, dinosaur, princess, superhero',
            'default_pages'  => 12,
            'options'        => [
                'child' => 'Child name (auto-generated if empty)',
                'pages' => 'Number of pages (default 12)',
                'ages'  => 'Target ages (e.g. 7 or 3,4,5,6,7,8)',
                'agama' => 'Religion tag (e.g. islam, kristen, katholik, hindu, budha)',
                'style' => 'Coloring style (simple, detailed, mandala)',
            ],
        ],

        'worksheet' => [
            'label'          => 'Worksheet',
            'emoji'          => '📝',
            'service'        => \App\Services\WorksheetGeneratorService::class,
            'argument'       => 'topic',
            'argument_label' => 'Worksheet topic',
            'argument_hint'  => 'e.g. matematika, bahasa, sains, logika',
            'default_pages'  => 8,
            'options'        => [
                'subtopic' => 'Specific subtopic (e.g. penjumlahan, pengurangan, huruf, angka)',
                'child'    => 'Child name (auto-generated if empty)',
                'pages'    => 'Number of pages (default 8)',
                'grades'   => 'Target grades (e.g. 1 or 1,2,3)',
                'agama'    => 'Religion tag (e.g. islam, kristen, katholik, hindu, budha)',
                'style'    => 'Worksheet type (practice, exam, activity)',
            ],
        ],

    ],

];
