<?php

namespace App\Enums;

enum EducationLevel: string
{
    case SD = 'sd';
    case SMP = 'smp';
    case SMA = 'sma';
    case SMK = 'smk';
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';
    case D4 = 'd4';
    case S1 = 's1';
    case S2 = 's2';
    case S3 = 's3';

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
            self::SD => 'SD',
            self::SMP => 'SMP',
            self::SMA => 'SMA',
            self::SMK => 'SMK',
            self::D1 => 'D1',
            self::D2 => 'D2',
            self::D3 => 'D3',
            self::D4 => 'D4',
            self::S1 => 'S1 (Sarjana)',
            self::S2 => 'S2 (Magister)',
            self::S3 => 'S3 (Doktor)',
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

    /**
     * Get order for sorting (lower = earlier education).
     */
    public function order(): int
    {
        return match ($this) {
            self::SD => 1,
            self::SMP => 2,
            self::SMA => 3,
            self::SMK => 4,
            self::D1 => 5,
            self::D2 => 6,
            self::D3 => 7,
            self::D4 => 8,
            self::S1 => 9,
            self::S2 => 10,
            self::S3 => 11,
        };
    }
}
