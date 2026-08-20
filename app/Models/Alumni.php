<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

class Alumni extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    #[Fillable([
        'nim',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'nomor_hp',
        'tahun_masuk',
        'tahun_lulus',
        'program_studi',
        'pekerjaan',
        'status_pekerjaan',
        'alamat',
        'keterangan'
    ])]
    protected $table = 'alumni';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nim',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'email',
        'nomor_hp',
        'tahun_masuk',
        'tahun_lulus',
        'program_studi',
        'pekerjaan',
        'status_pekerjaan',
        'alamat',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_masuk' => 'integer',
        'tahun_lulus' => 'integer',
    ];

    /**
     * Scope for searching alumni by name, nim, or email.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
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
     * Scope for filtering by status pekerjaan.
     */
    public function scopeByStatusPekerjaan($query, $status)
    {
        if ($status) {
            return $query->where('status_pekerjaan', $status);
        }
        return $query;
    }

    /**
     * Get gender display name.
     */
    public function getJenisKelaminDisplayAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
