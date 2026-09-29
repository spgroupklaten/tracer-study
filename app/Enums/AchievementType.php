<?php

namespace App\Enums;

enum AchievementType: string
{
    case Academic = 'akademik';
    case NonAcademic = 'non_akademik';

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
            self::Academic => 'Akademik',
            self::NonAcademic => 'Non-Akademik',
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
