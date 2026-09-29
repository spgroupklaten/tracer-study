<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'question_text',
        'type',
        'is_required',
        'is_active',
        'order',
        'options',
        'validation_rules',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => QuestionType::class,
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
        'options' => 'array',
        'validation_rules' => 'array',
    ];

    /**
     * Get the answers for this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TracerAnswer::class, 'question_id');
    }

    /**
     * Get the options for this question (relational).
     */
    public function questionOptions(): HasMany
    {
        return $this->hasMany(TracerQuestionOption::class, 'tracer_question_id');
    }

    /**
     * Get active options for this question.
     */
    public function activeQuestionOptions(): HasMany
    {
        return $this->hasMany(TracerQuestionOption::class, 'tracer_question_id')
            ->where('is_active', true)
            ->ordered();
    }

    /**
     * Get options array (backward compatibility with JSON field).
     */
    public function getOptionsArrayAttribute(): array
    {
        // Return empty array - options are now stored in tracer_question_options
        return [];
    }

    /**
     * Scope to order by order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to get only active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this is a choice type (radio, select, checkbox).
     */
    public function isChoiceType(): bool
    {
        return in_array($this->type, [
            QuestionType::Radio,
            QuestionType::Select,
            QuestionType::Checkbox,
        ]);
    }

    /**
     * Check if this requires an answer.
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Get options array.
     */
    public function getOptionsArray(): array
    {
        return $this->options ?? [];
    }
}
