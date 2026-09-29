<?php

namespace App\Enums;

enum TracerStudyStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';

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
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
        };
    }
}
