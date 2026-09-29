<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileContact extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'whatsapp',
        'website',
        'linkedin',
        'instagram',
        'facebook',
        'twitter',
        'github',
        'youtube',
    ];

    /**
     * Get the profile that owns this contact.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Check if a social media URL is set.
     */
    public function hasWhatsapp(): bool
    {
        return !empty($this->whatsapp);
    }

    /**
     * Check if website is set.
     */
    public function hasWebsite(): bool
    {
        return !empty($this->website);
    }

    /**
     * Check if LinkedIn is set.
     */
    public function hasLinkedin(): bool
    {
        return !empty($this->linkedin);
    }

    /**
     * Check if Instagram is set.
     */
    public function hasInstagram(): bool
    {
        return !empty($this->instagram);
    }

    /**
     * Check if Facebook is set.
     */
    public function hasFacebook(): bool
    {
        return !empty($this->facebook);
    }

    /**
     * Check if Twitter/X is set.
     */
    public function hasTwitter(): bool
    {
        return !empty($this->twitter);
    }

    /**
     * Check if GitHub is set.
     */
    public function hasGithub(): bool
    {
        return !empty($this->github);
    }

    /**
     * Check if YouTube is set.
     */
    public function hasYoutube(): bool
    {
        return !empty($this->youtube);
    }
}
