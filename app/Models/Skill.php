<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($skill) {
            if (empty($skill->slug)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }

    /**
     * Get the profiles that have this skill.
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profile_skills')
            ->withPivot([
                'level',
                'description',
            ])
            ->withTimestamps();
    }

    /**
     * Get skill by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get the level label from pivot.
     */
    public function getLevelLabelAttribute(): string
    {
        if (!$this->pivot || !$this->pivot->level) {
            return '-';
        }

        $level = \App\Enums\SkillLevel::tryFrom($this->pivot->level);
        return $level?->label() ?? $this->pivot->level;
    }
}
