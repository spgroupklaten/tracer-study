<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerQuestionOption extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tracer_question_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tracer_question_id',
        'label',
        'value',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the question that owns this option.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(TracerQuestion::class, 'tracer_question_id');
    }

    /**
     * Get the answers that use this option value.
     * Note: This is an indirect relationship through the question.
     * An answer is considered to use this option if the answer_value matches the option's value.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TracerAnswer::class, 'question_id');
    }

    /**
     * Scope to order by order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Scope to get only active options.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
