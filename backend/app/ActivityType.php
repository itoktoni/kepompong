<?php

namespace App;

use App\Concerns\EnumTrait;

enum ActivityType: string
{
    use EnumTrait;

    case STORYTELLING = 'storytelling';
    case BERMAIN_PERAN = 'bermain_peran';
    case PERMAINAN = 'permainan';
    case MONOLOG = 'monolog';
    case PROYEK_KREATIF = 'proyek_kreatif';
    case MUSIK_GERAK = 'musik_gerak';
    case PUZZLE = 'puzzle';
    case MINDFULNESS = 'mindfulness';
    case OUTDOOR = 'outdoor';
    case ILMU_PENGETAHUAN = 'ilmu_pengetahuan';
    case TEBAK_TEBAKAN = 'tebak_tebakan';
    case PERMAINAN_TANGAN = 'permainan_tangan';
    case LATIHAN_OTAK = 'latihan_otak';
    case KOMIK = 'komik';
    case WORKSHEET = 'worksheet';
    case COLORING = 'coloring';
    case MENGENAL_BENDA = 'mengenal_benda';

    public function description(): string
    {
        return match ($this) {
            self::STORYTELLING => 'Story Telling',
            self::BERMAIN_PERAN => 'Bermain Peran',
            self::PERMAINAN => 'Permainan',
            self::MONOLOG => 'Monolog',
            self::PROYEK_KREATIF => 'Proyek Kreatif & Seni',
            self::MUSIK_GERAK => 'Musik & Gerak',
            self::PUZZLE => 'Puzzle & Problem Solving',
            self::MINDFULNESS => 'Mindfulness & Refleksi',
            self::OUTDOOR => 'Outdoor Exploration',
            self::ILMU_PENGETAHUAN => 'Ilmu Pengetahuan & Literasi',
            self::TEBAK_TEBAKAN => 'Tebak-tebakan',
            self::PERMAINAN_TANGAN => 'Permainan Tangan',
            self::LATIHAN_OTAK => 'Latihan Otak',
            self::KOMIK => 'Komik Anak',
            self::WORKSHEET => 'Worksheet Anak',
            self::COLORING => 'Coloring',
            self::MENGENAL_BENDA => 'Mengenal Benda',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::STORYTELLING => '📖',
            self::BERMAIN_PERAN => '🎭',
            self::PERMAINAN => '🎲',
            self::MONOLOG => '🎤',
            self::PROYEK_KREATIF => '🎨',
            self::MUSIK_GERAK => '🎵',
            self::PUZZLE => '🧩',
            self::MINDFULNESS => '🧘',
            self::OUTDOOR => '🌿',
            self::ILMU_PENGETAHUAN => '🔬',
            self::TEBAK_TEBAKAN => '🤔',
            self::PERMAINAN_TANGAN => '🤲',
            self::LATIHAN_OTAK => '🧠',
            self::KOMIK => '💬',
            self::WORKSHEET => '📝',
            self::COLORING => '🖍️',
            self::MENGENAL_BENDA => '🪣',
        };
    }

    public function folder(): string
    {
        return "images/{$this->value}";
    }
}
