@extends('layouts.admin')

@section('title', 'Tambah Alumni')

@section('page-title', 'Alumni')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Tambah Alumni</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Tambah user + profile alumni baru (status: lulus)</p>
    </div>
    <a href="{{ route('admin.alumni.index') }}" class="btn btn-sm btn-default">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Form -->
<div class="card">
    <form action="{{ route('admin.alumni.store') }}" method="POST">
        @csrf

        <!-- Flash Errors -->
        @if($errors->any())
            <div class="m-4 p-3 bg-danger bg-opacity-10 border border-danger rounded">
                <div class="d-flex align-items-center gap-2 mb-2 text-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="fw-semibold">Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside mb-0" style="font-size: 0.875rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- User Data -->
        <div class="card-header py-3">
            <h6 class="mb-0 d-flex align-items-center gap-2">
                <span class="rounded d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem; background: #dbeafe;">
                    <i class="fas fa-user" style="color: #2563eb;"></i>
                </span>
                Data User
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label" style="font-size: 0.875rem;">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Contoh: Budi Santoso">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label" style="font-size: 0.875rem;">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="budi@email.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label" style="font-size: 0.875rem;">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" required
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 8 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label" style="font-size: 0.875rem;">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="form-control"
                           placeholder="Ulangi password">
                </div>
            </div>
        </div>

        <!-- Data Akademik -->
        <div class="card-header py-3">
            <h6 class="mb-0 d-flex align-items-center gap-2">
                <span class="rounded d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem; background: #ede9fe;">
                    <i class="fas fa-graduation-cap" style="color: #7c3aed;"></i>
                </span>
                Data Akademik
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nim" class="form-label" style="font-size: 0.875rem;">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required
                           class="form-control @error('nim') is-invalid @enderror"
                           placeholder="Contoh: 20190101">
                    @error('nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="fakultas" class="form-label" style="font-size: 0.875rem;">Fakultas</label>
                    <input type="text" name="fakultas" id="fakultas" value="{{ old('fakultas') }}"
                           class="form-control @error('fakultas') is-invalid @enderror"
                           placeholder="Fakultas Teknik">
                    @error('fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="program_studi" class="form-label" style="font-size: 0.875rem;">Program Studi</label>
                    <input type="text" name="program_studi" id="program_studi" value="{{ old('program_studi') }}"
                           class="form-control @error('program_studi') is-invalid @enderror"
                           placeholder="Teknik Informatika">
                    @error('program_studi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tahun_masuk" class="form-label" style="font-size: 0.875rem;">Tahun Masuk</label>
                    <input type="number" name="tahun_masuk" id="tahun_masuk" value="{{ old('tahun_masuk') }}"
                           min="1990" max="{{ date('Y') + 5 }}"
                           class="form-control @error('tahun_masuk') is-invalid @enderror">
                    @error('tahun_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tahun_lulus" class="form-label" style="font-size: 0.875rem;">Tahun Lulus</label>
                    <input type="number" name="tahun_lulus" id="tahun_lulus" value="{{ old('tahun_lulus') }}"
                           min="1990" max="{{ date('Y') + 5 }}"
                           class="form-control @error('tahun_lulus') is-invalid @enderror">
                    @error('tahun_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tanggal_lulus" class="form-label" style="font-size: 0.875rem;">Tanggal Lulus</label>
                    <input type="date" name="tanggal_lulus" id="tanggal_lulus" value="{{ old('tanggal_lulus') }}"
                           class="form-control @error('tanggal_lulus') is-invalid @enderror">
                    @error('tanggal_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="ipk" class="form-label" style="font-size: 0.875rem;">IPK</label>
                    <input type="number" name="ipk" id="ipk" value="{{ old('ipk') }}"
                           step="0.01" min="0" max="4"
                           class="form-control @error('ipk') is-invalid @enderror">
                    @error('ipk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="card-body p-4 pt-0">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Catatan:</strong> Alumni ini akan dibuat dengan <strong>role: mahasiswa</strong> dan <strong>status akademik: lulus</strong>.
                Data pribadi lainnya dapat dilengkapi oleh alumni sendiri melalui menu "Profil Saya".
                Data pekerjaan ada di modul Tracer Study.
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card-footer py-3 d-flex justify-content-end gap-2" style="background: #f9fafb;">
            <a href="{{ route('admin.alumni.index') }}" class="btn btn-default">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Alumni
            </button>
        </div>
    </form>
</div>
@endsection
