<?php

namespace App\Enums;

enum AchievementLevel: string
{
    case Internal = 'internal';
    case District = 'kabupaten';
    case Province = 'provinsi';
    case National = 'nasional';
    case International = 'internasional';

    /**
     * Get all values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Internal',
            self::District => 'Kabupaten/Kota',
            self::Province => 'Provinsi',
            self::National => 'Nasional',
            self::International => 'Internasional',
        };
    }

    /**
     * Get all options for dropdown.
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
