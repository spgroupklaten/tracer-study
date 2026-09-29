<?php

namespace App\Models;

use App\Enums\EducationLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'jenjang',
        'nama_institusi',
        'jurusan',
        'kota',
        'tahun_masuk',
        'tahun_lulus',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun_masuk' => 'integer',
        'tahun_lulus' => 'integer',
    ];

    /**
     * Get the profile that owns this education history.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get jenjang as enum if valid.
     */
    public function getJenjangLabelAttribute(): string
    {
        try {
            return EducationLevel::from($this->jenjang)->label();
        } catch (\ValueError $e) {
            return strtoupper($this->jenjang);
        }
    }

    /**
     * Get formatted tahun range.
     */
    public function getTahunRangeAttribute(): string
    {
        $tahunMasuk = $this->tahun_masuk;
        $tahunLulus = $this->tahun_lulus ?? 'Sekarang';
        return "{$tahunMasuk} - {$tahunLulus}";
    }
}
