@extends('layouts.admin')

@section('title', 'Data Mahasiswa')

@section('page-title', 'Data Mahasiswa')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Data Mahasiswa</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Kelola data mahasiswa yang terdaftar</p>
    </div>
    <div>
        <span class="badge bg-primary">
            <i class="fas fa-user-graduate me-1"></i>
            {{ $mahasiswas->total() }} Mahasiswa
        </span>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md">
                <label class="form-label">Cari</label>
                <div class="position-relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Nama atau email..."
                           class="form-control" style="padding-left: 2rem;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                </div>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
                @if($search)
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status Akun</th>
                        <th>Verifikasi</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $index => $mahasiswa)
                        <tr>
                            <td class="text-muted" style="font-size: 0.8125rem;">
                                {{ $mahasiswas->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2rem; height: 2rem; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        <span class="text-white" style="font-size: 0.75rem; font-weight: 600;">{{ strtoupper(substr($mahasiswa->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span style="font-weight: 500;">{{ $mahasiswa->name }}</span>
                                        @if($mahasiswa->profile)
                                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">NIM: {{ $mahasiswa->profile->nim ?? '-' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color: #6b7280;">{{ $mahasiswa->email }}</td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Aktif
                                </span>
                            </td>
                            <td>
                                @if($mahasiswa->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 0.8125rem;">
                                {{ $mahasiswa->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-user-graduate text-4xl mb-3 d-block" style="color: #d1d5db;"></i>
                                    <p class="mb-0">
                                        @if($search)
                                            Tidak ada hasil untuk "{{ $search }}"
                                        @else
                                            Belum ada data mahasiswa
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($mahasiswas->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size: 0.8125rem;">
                Menampilkan {{ $mahasiswas->firstItem() }} - {{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} data
            </div>
            <div>
                {{ $mahasiswas->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
