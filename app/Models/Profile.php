<?php

namespace App\Models;

use App\Enums\AcademicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'foto',
        'nim',
        'fakultas',
        'program_studi',
        'angkatan',
        'tahun_masuk',
        'tahun_lulus',
        'tanggal_lulus',
        'ipk',
        'academic_status',
        'no_hp',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        // Personal data from Phase 8.2
        'domisili',
        'status_pernikahan',
        'agama',
        'alamat_ktp',
        'alamat_tinggal',
        'hobi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_lulus' => 'date',
        'tahun_masuk' => 'integer',
        'tahun_lulus' => 'integer',
        'ipk' => 'decimal:2',
        'academic_status' => AcademicStatus::class,
    ];

    /**
     * Get the user that owns this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tracer study for this profile.
     */
    public function tracerStudy(): HasOne
    {
        return $this->hasOne(TracerStudy::class);
    }

    /**
     * Get the contact for this profile.
     */
    public function contact(): HasOne
    {
        return $this->hasOne(ProfileContact::class);
    }

    /**
     * Get the education histories for this profile.
     */
    public function educationHistories(): HasMany
    {
        return $this->hasMany(EducationHistory::class);
    }

    /**
     * Get the education histories sorted by tahun_lulus DESC.
     */
    public function educationHistoriesSorted(): HasMany
    {
        return $this->hasMany(EducationHistory::class)->orderByDesc('tahun_lulus');
    }

    /**
     * Get the skills for this profile.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'profile_skills')
            ->withPivot([
                'level',
                'description',
            ])
            ->withTimestamps();
    }

    /**
     * Get the achievements for this profile.
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class)->orderByDesc('tanggal');
    }

    /**
     * Check if profile is aktif (mahasiswa).
     */
    public function isAktif(): bool
    {
        return $this->academic_status === AcademicStatus::Aktif;
    }

    /**
     * Check if profile is lulus (alumni).
     */
    public function isLulus(): bool
    {
        return $this->academic_status === AcademicStatus::Lulus;
    }

    /**
     * Get the academic status label.
     */
    public function getAcademicStatusLabelAttribute(): string
    {
        return $this->academic_status->label();
    }

    /**
     * Check if profile has a tracer study.
     */
    public function hasTracerStudy(): bool
    {
        return $this->tracerStudy()->exists();
    }

    /**
     * Check if tracer study is submitted.
     */
    public function isTracerStudySubmitted(): bool
    {
        return $this->tracerStudy?->isSubmitted() ?? false;
    }

    /**
     * Get tracer study status label.
     */
    public function getTracerStudyStatusLabelAttribute(): ?string
    {
        return $this->tracerStudy?->status_label;
    }

    /**
     * Scope for filtering aktif (mahasiswa) profiles.
     */
    public function scopeAktif($query)
    {
        return $query->where('academic_status', AcademicStatus::Aktif);
    }

    /**
     * Scope for filtering lulus (alumni) profiles.
     */
    public function scopeLulus($query)
    {
        return $query->where('academic_status', AcademicStatus::Lulus);
    }

    /**
     * Scope for searching profile by name, nim, or program studi.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('program_studi', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by program studi.
     */
    public function scopeByProgramStudi($query, $program_studi)
    {
        if ($program_studi) {
            return $query->where('program_studi', $program_studi);
        }
        return $query;
    }

    /**
     * Scope for filtering by tahun lulus.
     */
    public function scopeByTahunLulus($query, $tahun)
    {
        if ($tahun) {
            return $query->where('tahun_lulus', $tahun);
        }
        return $query;
    }

    /**
     * Get gender display name.
     */
    public function getJenisKelaminDisplayAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Alias for tahun_masuk (for compatibility with existing views/controllers).
     */
    public function getTahunMasukAttribute($value): int
    {
        return $value;
    }

    /**
     * Get nama_lengkap from related user.
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    /**
     * Get email from related user.
     */
    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }
}
