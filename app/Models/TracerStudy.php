<?php

namespace App\Models;

use App\Enums\TracerStudyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerStudy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alumni_id',
        'profile_id',
        'status',
        'started_at',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TracerStudyStatus::class,
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the alumni that owns the tracer study (legacy).
     */
    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    /**
     * Get the profile that owns the tracer study.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the answers for this tracer study.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(TracerAnswer::class);
    }

    /**
     * Check if tracer study is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === TracerStudyStatus::Draft;
    }

    /**
     * Check if tracer study is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === TracerStudyStatus::Submitted;
    }

    /**
     * Mark as submitted.
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => TracerStudyStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            TracerStudyStatus::Draft => 'Draft',
            TracerStudyStatus::Submitted => 'Submitted',
        };
    }
}
