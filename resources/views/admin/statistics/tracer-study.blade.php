@extends('layouts.admin')

@section('title', 'Statistik Tracer Study')

@section('page-title', 'Statistik')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Statistik Tracer Study</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Statistik berdasarkan data yang tersimpan</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Total Alumni</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $totalAlumni }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #dbeafe;">
                    <i class="fas fa-users" style="color: #2563eb;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Sudah Mengisi</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $totalTracerStudy }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #dcfce7;">
                    <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Belum Mengisi</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #ea580c;">{{ $alumniBelumMengisi }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #ffedd5;">
                    <i class="fas fa-clock" style="color: #ea580c;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Partisipasi</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #6366f1;">{{ $persentasePartisipasi }}%</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #e0e7ff;">
                    <i class="fas fa-chart-pie" style="color: #6366f1;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="row g-3">
    <!-- Status Distribution -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="fas fa-chart-pie me-2 text-muted"></i>Distribusi Status Pekerjaan
            </div>
            <div class="card-body p-3">
                @if($distribusiPekerjaan->count() > 0)
                    <div class="mb-2">
                        @foreach($distribusiPekerjaan as $item)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8125rem;">
                                    <span style="font-weight: 500; color: #374151;">{{ $item->status_pekerjaan ?: 'Tidak Diketahui' }}</span>
                                    <span style="color: #6b7280;">{{ $item->total }} ({{ $totalAlumni > 0 ? round($item->total / $totalAlumni * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress" style="height: 0.375rem;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $totalAlumni > 0 ? ($item->total / $totalAlumni) * 100 : 0 }}%; background: #2563eb;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0 py-4">Belum ada data distribusi</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Program Studi Distribution -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-2">
                <i class="fas fa-graduation-cap me-2 text-muted"></i>Distribusi Program Studi
            </div>
            <div class="card-body p-3">
                @if($statistikProgramStudi->count() > 0)
                    <div class="mb-2">
                        @foreach($statistikProgramStudi as $ps)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8125rem;">
                                    <span style="font-weight: 500; color: #374151;">{{ $ps->program_studi }}</span>
                                    <span style="color: #6b7280;">{{ $ps->total_alumni }}</span>
                                </div>
                                <div class="progress" style="height: 0.375rem;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $totalAlumni > 0 ? ($ps->total_alumni / $totalAlumni) * 100 : 0 }}%; background: #16a34a;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0 py-4">Belum ada data program studi</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
