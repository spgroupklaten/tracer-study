@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')

@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="rounded" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 1.25rem;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="mb-1" style="color: #ffffff; font-size: 1.375rem; font-weight: 600;">
                Selamat Datang, {{ Auth::user()->name }}!
            </h2>
            <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 0.875rem;">
                Anda login sebagai <span class="badge" style="background: rgba(255,255,255,0.2); color: #ffffff;">Mahasiswa</span>
            </p>
        </div>
        @if($tracerStudyStatus === 'not_found')
            <a href="{{ route('mahasiswa.tracer-study.create') }}"
               class="btn" style="background: #ffffff; color: #16a34a; font-weight: 500; padding: 0.5rem 1rem;">
                <i class="fas fa-edit me-1"></i> Isi Tracer Study
            </a>
        @elseif($tracerStudyStatus === 'draft')
            <a href="{{ route('mahasiswa.tracer-study.create') }}"
               class="btn" style="background: #ffffff; color: #ca8a04; font-weight: 500; padding: 0.5rem 1rem;">
                <i class="fas fa-pencil-alt me-1"></i> Lanjutkan Tracer Study
            </a>
        @else
            <a href="{{ route('mahasiswa.tracer-study.index') }}"
               class="btn" style="background: #ffffff; color: #16a34a; font-weight: 500; padding: 0.5rem 1rem;">
                <i class="fas fa-eye me-1"></i> Lihat Tracer Study
            </a>
        @endif
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row g-4 mt-4">
    <!-- Status Tracer Study -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Status Tracer Study</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: @if($tracerStudyStatus === 'submitted') #16a34a @elseif($tracerStudyStatus === 'draft') #ca8a04 @else #6b7280 @endif; line-height: 1.1;">
                        @if($tracerStudyStatus === 'submitted')
                            Sudah Dikirim
                        @elseif($tracerStudyStatus === 'draft')
                            Draft
                        @else
                            Belum Diisi
                        @endif
                    </h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">
                        @if($tracerStudyStatus === 'submitted')
                            Tracer study telah submitted
                        @elseif($tracerStudyStatus === 'draft')
                            Simpan draft untuk nanti
                        @else
                            Segera isi tracer study
                        @endif
                    </p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: @if($tracerStudyStatus === 'submitted') #dcfce7 @elseif($tracerStudyStatus === 'draft') #fef9c3 @else #f3f4f6 @endif; flex-shrink: 0;">
                    <i class="fas @if($tracerStudyStatus === 'submitted') fa-check-circle @elseif($tracerStudyStatus === 'draft') fa-clock @else fa-clipboard-list @endif" style="color: @if($tracerStudyStatus === 'submitted') #16a34a @elseif($tracerStudyStatus === 'draft') #ca8a04 @else #6b7280 @endif; font-size: 1rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Status -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Status Profil</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">
                        {{ $profileStatus }}
                    </h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">
                        @if($profileStatus === 'Lengkap')
                            Data profil telah lengkap
                        @else
                            Lengkapi data profil Anda
                        @endif
                    </p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #e0e7ff; flex-shrink: 0;">
                    <i class="fas fa-user-circle" style="color: #6366f1; font-size: 1rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Questions -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stats-card" style="padding: 1.125rem;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.8125rem; font-weight: 500;">Pertanyaan Diisi</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827; line-height: 1.1;">
                        {{ $completedQuestions }} / {{ $totalQuestions }}
                    </h3>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.75rem;">
                        {{ $totalQuestions > 0 ? round(($completedQuestions / $totalQuestions) * 100) : 0 }}% selesai
                    </p>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #f3e8ff; flex-shrink: 0;">
                    <i class="fas fa-list-check" style="color: #9333ea; font-size: 1rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="row g-4 mt-4">
    <!-- Profile Info -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-2">
                <span><i class="fas fa-user me-2 text-muted"></i>Profil Akademik</span>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <dl class="mb-0">
                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                        <dt style="font-size: 0.8125rem; color: #6b7280;">Nama</dt>
                        <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ Auth::user()->name }}</dd>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                        <dt style="font-size: 0.8125rem; color: #6b7280;">Email</dt>
                        <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ Auth::user()->email }}</dd>
                    </div>
                    @if($profile)
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                            <dt style="font-size: 0.8125rem; color: #6b7280;">NIM</dt>
                            <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $profile->nim }}</dd>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                            <dt style="font-size: 0.8125rem; color: #6b7280;">Program Studi</dt>
                            <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $profile->program_studi }}</dd>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                            <dt style="font-size: 0.8125rem; color: #6b7280;">Angkatan</dt>
                            <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $profile->angkatan }}</dd>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <dt style="font-size: 0.8125rem; color: #6b7280;">Status</dt>
                            <dd class="mb-0" style="font-size: 0.8125rem; font-weight: 500; color: #111827;">{{ $profile->academic_status_label }}</dd>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.8125rem;">Profil belum dilengkapi</p>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-2">
                <span><i class="fas fa-bolt me-2 text-muted"></i>Aksi Cepat</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-6">
                        @if($tracerStudyStatus === 'not_found')
                            <a href="{{ route('mahasiswa.tracer-study.create') }}" class="btn btn-light w-100 text-start py-2 px-3">
                                <i class="fas fa-edit me-2" style="color: #16a34a;"></i>
                                <span style="font-size: 0.8125rem; font-weight: 500;">Isi Tracer Study</span>
                            </a>
                        @elseif($tracerStudyStatus === 'draft')
                            <a href="{{ route('mahasiswa.tracer-study.create') }}" class="btn btn-light w-100 text-start py-2 px-3">
                                <i class="fas fa-pencil-alt me-2" style="color: #ca8a04;"></i>
                                <span style="font-size: 0.8125rem; font-weight: 500;">Lanjutkan</span>
                            </a>
                        @else
                            <a href="{{ route('mahasiswa.tracer-study.index') }}" class="btn btn-light w-100 text-start py-2 px-3">
                                <i class="fas fa-eye me-2" style="color: #16a34a;"></i>
                                <span style="font-size: 0.8125rem; font-weight: 500;">Lihat Tracer Study</span>
                            </a>
                        @endif
                    </div>
                    <div class="col-6">
                        <a href="{{ route('home') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-home me-2" style="color: #2563eb;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Beranda</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.statistics') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-chart-bar me-2" style="color: #9333ea;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Statistik Alumni</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('alumni.index') }}" class="btn btn-light w-100 text-start py-2 px-3">
                            <i class="fas fa-users me-2" style="color: #6366f1;"></i>
                            <span style="font-size: 0.8125rem; font-weight: 500;">Profil Alumni</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
