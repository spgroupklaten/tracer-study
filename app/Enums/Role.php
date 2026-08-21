<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Mahasiswa = 'mahasiswa';

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
            self::Admin => 'Administrator',
            self::Mahasiswa => 'Mahasiswa',
        };
    }
}
