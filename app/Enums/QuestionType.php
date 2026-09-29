<?php

namespace App\Enums;

enum QuestionType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Number = 'number';
    case Radio = 'radio';
    case Select = 'select';
    case Checkbox = 'checkbox';
    case Date = 'date';

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
            self::Text => 'Text Input',
            self::Textarea => 'Text Area',
            self::Number => 'Number',
            self::Radio => 'Single Choice (Radio)',
            self::Select => 'Dropdown (Select)',
            self::Checkbox => 'Multiple Choice (Checkbox)',
            self::Date => 'Date',
        };
    }
}
