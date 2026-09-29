@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="rounded" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 1.25rem;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="mb-1" style="color: #ffffff; font-size: 1.375rem; font-weight: 600;">
                Selamat Datang, {{ Auth::user()->name }}!
            </h2>
            <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 0.875rem;">
                Anda login sebagai <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff;">{{ Auth::user()->role->label() }}</span>
            </p>
        </div>
        <a href="{{ route('admin.alumni.create') }}"
           class="btn" style="background: #ffffff; color: #2563eb; font-weight: 500; padding: 0.5rem 1rem;">
            <i class="fas fa-plus me-1"></i> Tambah Alumni
        </a>
    </div>
</div>

<!-- Statistics Cards Row 1 -->
<div class="row g-4 mt-4">
    <!-- Total Alumni -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Total Alumni</p>
                    <h3 class="mb-0" style="font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $totalAlumni }}</h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">Mahasiswa terdaftar</p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #dbeafe; flex-shrink: 0;">
                    <i class="fas fa-users" style="color: #2563eb; font-size: 1rem;"></i>
                </div>
            </div>
            <div class="mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                <a href="{{ route('admin.alumni.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 0.8125rem; font-weight: 500; color: #2563eb;">
                    Lihat Data Alumni <i class="fas fa-arrow-right" style="font-size: 0.6875rem;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Mahasiswa -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Total Mahasiswa</p>
                    <h3 class="mb-0" style="font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $totalMahasiswa }}</h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">User mahasiswa</p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #dcfce7; flex-shrink: 0;">
                    <i class="fas fa-user-graduate" style="color: #16a34a; font-size: 1rem;"></i>
                </div>
            </div>
            <div class="mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                <a href="{{ route('admin.mahasiswa.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 0.8125rem; font-weight: 500; color: #16a34a;">
                    Lihat Data Mahasiswa <i class="fas fa-arrow-right" style="font-size: 0.6875rem;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Tracer Study -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Tracer Study</p>
                    <h3 class="mb-0" style="font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $totalAlumni }}</h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">Total alumni</p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #e0e7ff; flex-shrink: 0;">
                    <i class="fas fa-clipboard-list" style="color: #6366f1; font-size: 1rem;"></i>
                </div>
            </div>
            <div class="mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                <a href="{{ route('admin.tracer-study.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 0.8125rem; font-weight: 500; color: #6366f1;">
                    Lihat Tracer Study <i class="fas fa-arrow-right" style="font-size: 0.6875rem;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Belum Bekerja -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Belum Bekerja</p>
                    <h3 class="mb-0" style="font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $alumniBelumBekerja }}</h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">Sedang mencari</p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #fef9c3; flex-shrink: 0;">
                    <i class="fas fa-clock" style="color: #ca8a04; font-size: 1rem;"></i>
                </div>
            </div>
            <div class="mt-3 pt-2" style="border-top: 1px solid #f0f0f0;">
                <a href="{{ route('admin.alumni.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 0.8125rem; font-weight: 500; color: #ca8a04;">
                    Lihat Semua <i class="fas fa-arrow-right" style="font-size: 0.6875rem;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div class="row g-4 mt-4">
    <!-- Bekerja -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stats-card" style="padding: 1rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Bekerja</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $alumniBekerja }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.25rem; height: 2.25rem; background: #dcfce7; flex-shrink: 0;">
                    <i class="fas fa-briefcase" style="color: #16a34a; font-size: 0.875rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Studi Lanjut -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stats-card" style="padding: 1rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Studi Lanjut</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $alumniStudiLanjut }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.25rem; height: 2.25rem; background: #f3e8ff; flex-shrink: 0;">
                    <i class="fas fa-graduation-cap" style="color: #9333ea; font-size: 0.875rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="stats-card" style="padding: 1rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Admin</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">{{ $totalAdmin }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.25rem; height: 2.25rem; background: #dbeafe; flex-shrink: 0;">
                    <i class="fas fa-user-shield" style="color: #2563eb; font-size: 0.875rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="row g-4 mt-4">
    <!-- Program Studi -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-2">
                <span><i class="fas fa-chart-bar me-2 text-muted"></i>Program Studi Populer</span>
            </div>
            <div class="card-body" style="padding: 1rem;">
                @if($programStudis->count() > 0)
                    @foreach($programStudis as $ps)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.875rem;">
                                <span style="font-weight: 600; color: #374151;">{{ $ps->program_studi }}</span>
                                <span style="color: #6b7280; font-weight: 500;">{{ $ps->total }} alumni</span>
                            </div>
                            <div class="progress" style="height: 0.375rem;">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($ps->total / $totalAlumni) * 100 }}%; background: #2563eb;"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0 py-4">Belum ada data program studi</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-2">
                <span><i class="fas fa-bolt me-2 text-muted"></i>Aksi Cepat</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('admin.alumni.index') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-users me-2" style="color: #2563eb;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Kelola Alumni</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.alumni.create') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-user-plus me-2" style="color: #16a34a;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Tambah Alumni</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.statistics') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-chart-pie me-2" style="color: #9333ea;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Statistik</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.tracer-study.index') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-clipboard-list me-2" style="color: #6366f1;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Tracer Study</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.alumni.export') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-file-export me-2" style="color: #ea580c;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Export CSV</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.kuesioner.index') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-question-circle me-2" style="color: #0891b2;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Kuesioner</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
