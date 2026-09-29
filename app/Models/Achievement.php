<?php

namespace App\Models;

use App\Enums\AchievementLevel;
use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Achievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'judul',
        'jenis',
        'tingkat',
        'penyelenggara',
        'tanggal',
        'tahun',
        'deskripsi',
        'certificate_path',
        'certificate_original_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        'tahun' => 'integer',
        'jenis' => AchievementType::class,
        'tingkat' => AchievementLevel::class,
    ];

    /**
     * Get the profile that owns this achievement.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the jenis label.
     */
    public function getJenisLabelAttribute(): string
    {
        return $this->jenis->label();
    }

    /**
     * Get the tingkat label.
     */
    public function getTingkatLabelAttribute(): string
    {
        return $this->tingkat->label();
    }

    /**
     * Get the certificate URL if exists.
     */
    public function getCertificateUrlAttribute(): ?string
    {
        if ($this->certificate_path && Storage::disk('public')->exists($this->certificate_path)) {
            return Storage::url($this->certificate_path);
        }
        return null;
    }

    /**
     * Check if has certificate.
     */
    public function hasCertificate(): bool
    {
        return !empty($this->certificate_path) && Storage::disk('public')->exists($this->certificate_path);
    }

    /**
     * Delete the associated certificate file.
     */
    public function deleteCertificate(): bool
    {
        if ($this->certificate_path && Storage::disk('public')->exists($this->certificate_path)) {
            return Storage::disk('public')->delete($this->certificate_path);
        }
        return false;
    }
}
