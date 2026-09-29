<?php

namespace App\Enums;

enum AcademicStatus: string
{
    case Aktif = 'aktif';
    case Lulus = 'lulus';

    /**
     * Get the label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Mahasiswa Aktif',
            self::Lulus => 'Alumni',
        };
    }

    /**
     * Get all values as array for dropdowns.
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
