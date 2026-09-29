@extends('layouts.admin')

@section('title', 'Data Alumni')

@section('page-title', 'Data Alumni')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Data Alumni</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Kelola data alumni ({{ $profiles->total() }} alumni)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.alumni.export') }}" class="btn btn-success btn-sm">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Alumni
        </a>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.alumni.index') }}" method="GET" class="row g-3">
            <!-- Search -->
            <div class="col-12 col-md-3">
                <label for="search" class="form-label">Cari</label>
                <div class="position-relative">
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ $search }}"
                           placeholder="Nama, Email, NIM..."
                           class="form-control" style="padding-left: 2rem;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                </div>
            </div>

            <!-- Program Studi Filter -->
            <div class="col-6 col-md-2">
                <label for="program_studi" class="form-label">Program Studi</label>
                <select name="program_studi" id="program_studi" class="form-control form-select">
                    <option value="">Semua</option>
                    @foreach($programStudis as $ps)
                        <option value="{{ $ps }}" {{ $program_studi == $ps ? 'selected' : '' }}>{{ $ps }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tahun Lulus Filter -->
            <div class="col-6 col-md-2">
                <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                <select name="tahun_lulus" id="tahun_lulus" class="form-control form-select">
                    <option value="">Semua</option>
                    @foreach($tahunLulusOptions as $tahun)
                        <option value="{{ $tahun }}" {{ $tahun_lulus == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fakultas Filter -->
            <div class="col-6 col-md-2">
                <label for="fakultass" class="form-label">Fakultas</label>
                <select name="fakultass" id="fakultass" class="form-control form-select">
                    <option value="">Semua</option>
                    @foreach($fakultassList as $fak)
                        <option value="{{ $fak }}" {{ $fakultass == $fak ? 'selected' : '' }}>{{ $fak }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page -->
            <div class="col-6 col-md-1">
                <label for="per_page" class="form-label">Tampilkan</label>
                <select name="per_page" id="per_page" class="form-control form-select">
                    @foreach([10, 25, 50, 100] as $perPageOption)
                        <option value="{{ $perPageOption }}" {{ $perPage == $perPageOption ? 'selected' : '' }}>{{ $perPageOption }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.alumni.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Tahun Lulus</th>
                        <th>Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $index => $profile)
                        <tr>
                            <td class="text-muted" style="font-size: 0.8125rem;">
                                {{ $profiles->firstItem() + $index }}
                            </td>
                            <td style="font-weight: 500;">{{ $profile->nim }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($profile->foto && Storage::disk('public')->exists($profile->foto))
                                        <img src="{{ Storage::url($profile->foto) }}"
                                             alt="{{ $profile->nama_lengkap }}"
                                             class="rounded-circle"
                                             style="width: 2rem; height: 2rem; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                            <span class="text-white" style="font-size: 0.75rem; font-weight: 600;">{{ strtoupper(substr($profile->nama_lengkap, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <span style="font-weight: 500;">{{ $profile->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td style="color: #6b7280;">{{ $profile->program_studi ?? '-' }}</td>
                            <td style="color: #6b7280;">{{ $profile->tahun_lulus ?? '-' }}</td>
                            <td>
                                @if($profile->tracerStudy)
                                    @if($profile->tracerStudy->isSubmitted())
                                        <span class="badge bg-success">Sudah Tracer</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Belum</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.alumni.show', $profile) }}" class="btn btn-xs btn-outline-secondary" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.alumni.edit', $profile) }}" class="btn btn-xs btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.alumni.destroy', $profile) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alumni ini? Semua data terkait akan ikut dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users text-4xl mb-3 d-block" style="color: #d1d5db;"></i>
                                    <p class="mb-0">Belum ada data alumni</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($profiles->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size: 0.8125rem;">
                Menampilkan {{ $profiles->firstItem() }} - {{ $profiles->lastItem() }} dari {{ $profiles->total() }} data
            </div>
            <div>
                {{ $profiles->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
