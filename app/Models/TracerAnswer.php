<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TracerAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tracer_study_id',
        'question_id',
        'answer_value',
    ];

    /**
     * Get the tracer study that owns the answer.
     */
    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }

    /**
     * Get the question that this answer belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(TracerQuestion::class, 'question_id');
    }

    /**
     * Check if answer is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->answer_value);
    }

    /**
     * Get answer value or default.
     */
    public function getValueOrDefault($default = null)
    {
        return $this->answer_value ?? $default;
    }
}
