@extends('layouts.admin')

@section('title', 'Detail Alumni')

@section('page-title', 'Detail Alumni')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Detail Alumni</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">{{ $alumni->nama_lengkap }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.alumni.edit', $alumni) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<!-- Profile Card -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3">
            @if($alumni->foto && Storage::disk('public')->exists($alumni->foto))
                <img src="{{ Storage::url($alumni->foto) }}"
                     alt="{{ $alumni->nama_lengkap }}"
                     class="rounded-circle"
                     style="width: 4rem; height: 4rem; object-fit: cover;">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                    <span class="text-white" style="font-size: 1.5rem; font-weight: 700;">{{ strtoupper(substr($alumni->nama_lengkap, 0, 1)) }}</span>
                </div>
            @endif
            <div class="flex-grow-1 text-center text-sm-start">
                <h2 class="mb-1" style="font-size: 1.125rem; font-weight: 600; color: #111827;">{{ $alumni->nama_lengkap }}</h2>
                <p class="mb-2 text-muted" style="font-size: 0.875rem;">{{ $alumni->nim }} &bull; {{ $alumni->program_studi ?? '-' }}</p>
                @if($alumni->tracerStudy)
                    @if($alumni->tracerStudy->isSubmitted())
                        <span class="badge bg-success">Sudah Mengisi Tracer Study</span>
                    @else
                        <span class="badge bg-warning">Draft Tracer Study</span>
                    @endif
                @else
                    <span class="badge bg-secondary">Belum Mengisi Tracer Study</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detail Info -->
<div class="row g-4">
    <!-- Data Akademik -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-graduation-cap me-2 text-primary"></i>Data Akademik
            </div>
            <div class="card-body p-3">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted border-0" style="width: 140px;">NIM</td>
                        <td class="fw-medium border-0">{{ $alumni->nim }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Nama Lengkap</td>
                        <td class="fw-medium border-0">{{ $alumni->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Email</td>
                        <td class="fw-medium border-0">
                            <a href="mailto:{{ $alumni->email }}" class="text-decoration-none">{{ $alumni->email ?? '-' }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Fakultas</td>
                        <td class="fw-medium border-0">{{ $alumni->fakultas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Program Studi</td>
                        <td class="fw-medium border-0">{{ $alumni->program_studi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Angkatan</td>
                        <td class="fw-medium border-0">{{ $alumni->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Tahun Masuk</td>
                        <td class="fw-medium border-0">{{ $alumni->tahun_masuk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Tahun Lulus</td>
                        <td class="fw-medium border-0">{{ $alumni->tahun_lulus ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Tanggal Lulus</td>
                        <td class="fw-medium border-0">{{ $alumni->tanggal_lulus ? $alumni->tanggal_lulus->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">IPK</td>
                        <td class="fw-medium border-0">{{ $alumni->ipk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Status</td>
                        <td class="fw-medium border-0">
                            <span class="badge bg-{{ $alumni->isLulus() ? 'success' : 'info' }}">
                                {{ $alumni->academicStatusLabel }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Data Pribadi -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-user me-2 text-success"></i>Data Pribadi
            </div>
            <div class="card-body p-3">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted border-0" style="width: 140px;">NIK</td>
                        <td class="fw-medium border-0">{{ $alumni->nik ? Str::mask($alumni->nik, '*', -8) : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Jenis Kelamin</td>
                        <td class="fw-medium border-0">{{ $alumni->jenisKelaminDisplay }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Tempat, Tgl Lahir</td>
                        <td class="fw-medium border-0">{{ $alumni->tempat_lahir ?? '-' }}, {{ $alumni->tanggal_lahir ? $alumni->tanggal_lahir->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Status Pernikahan</td>
                        <td class="fw-medium border-0">{{ $alumni->status_pernikahan ? App\Helpers\EnumHelper::statusPernikahan($alumni->status_pernikahan) : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Agama</td>
                        <td class="fw-medium border-0">{{ $alumni->agama ? App\Helpers\EnumHelper::agama($alumni->agama) : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">No. HP</td>
                        <td class="fw-medium border-0">{{ $alumni->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Domisili</td>
                        <td class="fw-medium border-0">{{ $alumni->domisili ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Alamat</td>
                        <td class="fw-medium border-0">{{ $alumni->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted border-0">Hobi</td>
                        <td class="fw-medium border-0">{{ $alumni->hobi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Pendidikan -->
@if($alumni->educationHistories->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-school me-2 text-info"></i>Riwayat Pendidikan
    </div>
    <div class="card-body p-3">
        <div class="row">
            @foreach($alumni->educationHistoriesSorted as $edu)
                <div class="col-md-6 mb-2">
                    <div class="border rounded p-2">
                        <strong>{{ $edu->jenjang_label }}</strong> - {{ $edu->nama_institusi }}
                        @if($edu->jurusan)
                            <br><small class="text-muted">{{ $edu->jurusan }}</small>
                        @endif
                        <br><small class="text-muted">{{ $edu->tahun_range }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Keahlian -->
@if($alumni->skills->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-tools me-2 text-warning"></i>Keahlian / Skill
    </div>
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            @foreach($alumni->skills as $skill)
                <span class="badge bg-secondary">
                    {{ $skill->name }}
                    <span class="text-light opacity-75">({{ $skill->pivot->level }})</span>
                </span>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Prestasi -->
@if($alumni->achievements->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-trophy me-2 text-primary"></i>Prestasi
    </div>
    <div class="card-body p-3">
        <div class="list-group list-group-flush">
            @foreach($alumni->achievements as $achievement)
                <div class="list-group-item px-0 py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $achievement->judul }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $achievement->jenis_label }} &bull;
                                {{ $achievement->tingkat_label }}
                                @if($achievement->penyelenggara)
                                    &bull; {{ $achievement->penyelenggara }}
                                @endif
                                @if($achievement->tahun)
                                    &bull; {{ $achievement->tahun }}
                                @endif
                            </small>
                        </div>
                        @if($achievement->hasCertificate())
                            <a href="{{ Storage::url($achievement->certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        @endif
                    </div>
                    @if($achievement->deskripsi)
                        <p class="small text-muted mb-0 mt-1">{{ $achievement->deskripsi }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Timestamps -->
<div class="mt-4 text-muted" style="font-size: 0.75rem;">
    <i class="far fa-calendar-plus me-1"></i> Dibuat: {{ $alumni->created_at->format('d M Y, H:i') }}
    &nbsp;&nbsp;
    <i class="far fa-calendar-check me-1"></i> Diperbarui: {{ $alumni->updated_at->format('d M Y, H:i') }}
</div>
@endsection
