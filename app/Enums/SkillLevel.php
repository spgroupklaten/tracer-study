<?php

namespace App\Enums;

enum SkillLevel: string
{
    case Beginner = 'pemula';
    case Intermediate = 'menengah';
    case Advanced = 'mahir';
    case Expert = 'ahli';

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
            self::Beginner => 'Pemula',
            self::Intermediate => 'Menengah',
            self::Advanced => 'Mahir',
            self::Expert => 'Ahli',
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
     * Get order for sorting.
     */
    public function order(): int
    {
        return match ($this) {
            self::Beginner => 1,
            self::Intermediate => 2,
            self::Advanced => 3,
            self::Expert => 4,
        };
    }
}
