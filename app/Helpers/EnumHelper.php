<?php

namespace App\Helpers;

class EnumHelper
{
    /**
     * Get label for status pernikahan.
     */
    public static function statusPernikahan(?string $value): string
    {
        return match ($value) {
            'belum_menikah' => 'Belum Menikah',
            'menikah' => 'Menikah',
            'cerai_hidup' => 'Cerai Hidup',
            'cerai_mati' => 'Cerai Mati',
            default => '-',
        };
    }

    /**
     * Get label for agama.
     */
    public static function agama(?string $value): string
    {
        return match ($value) {
            'islam' => 'Islam',
            'kristen' => 'Kristen',
            'katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'konghucu' => 'Konghucu',
            'lainnya' => 'Lainnya',
            default => '-',
        };
    }
}
