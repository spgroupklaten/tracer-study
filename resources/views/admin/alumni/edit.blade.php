@extends('layouts.admin')

@section('title', 'Edit Alumni')

@section('page-title', 'Alumni')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Edit Alumni</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Edit data akademik alumni</p>
    </div>
    <a href="{{ route('admin.alumni.index') }}" class="btn btn-sm btn-default">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Form -->
<div class="card">
    <form action="{{ route('admin.alumni.update', ['profile' => $alumni->id]) }}" method="POST">
        @csrf
        @method('PUT')

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
                    <label for="name" class="form-label" style="font-size: 0.875rem;">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $alumni->user->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label" style="font-size: 0.875rem;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $alumni->user->email ?? '') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    <label for="nim" class="form-label" style="font-size: 0.875rem;">NIM</label>
                    <input type="text" name="nim" id="nim" value="{{ old('nim', $alumni->nim) }}"
                           class="form-control @error('nim') is-invalid @enderror">
                    @error('nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="fakultas" class="form-label" style="font-size: 0.875rem;">Fakultas</label>
                    <input type="text" name="fakultas" id="fakultas" value="{{ old('fakultas', $alumni->fakultas) }}"
                           class="form-control @error('fakultas') is-invalid @enderror">
                    @error('fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="program_studi" class="form-label" style="font-size: 0.875rem;">Program Studi</label>
                    <input type="text" name="program_studi" id="program_studi" value="{{ old('program_studi', $alumni->program_studi) }}"
                           class="form-control @error('program_studi') is-invalid @enderror">
                    @error('program_studi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tahun_masuk" class="form-label" style="font-size: 0.875rem;">Tahun Masuk</label>
                    <input type="number" name="tahun_masuk" id="tahun_masuk" value="{{ old('tahun_masuk', $alumni->tahun_masuk) }}"
                           min="1990" max="{{ date('Y') + 5 }}"
                           class="form-control @error('tahun_masuk') is-invalid @enderror">
                    @error('tahun_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tahun_lulus" class="form-label" style="font-size: 0.875rem;">Tahun Lulus</label>
                    <input type="number" name="tahun_lulus" id="tahun_lulus" value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}"
                           min="1990" max="{{ date('Y') + 5 }}"
                           class="form-control @error('tahun_lulus') is-invalid @enderror">
                    @error('tahun_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="tanggal_lulus" class="form-label" style="font-size: 0.875rem;">Tanggal Lulus</label>
                    <input type="date" name="tanggal_lulus" id="tanggal_lulus" value="{{ old('tanggal_lulus', $alumni->tanggal_lulus?->format('Y-m-d')) }}"
                           class="form-control @error('tanggal_lulus') is-invalid @enderror">
                    @error('tanggal_lulus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="ipk" class="form-label" style="font-size: 0.875rem;">IPK</label>
                    <input type="number" name="ipk" id="ipk" value="{{ old('ipk', $alumni->ipk) }}"
                           step="0.01" min="0" max="4"
                           class="form-control @error('ipk') is-invalid @enderror">
                    @error('ipk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="academic_status" class="form-label" style="font-size: 0.875rem;">Status Akademik</label>
                    <select name="academic_status" id="academic_status" class="form-select @error('academic_status') is-invalid @enderror">
                        <option value="aktif" {{ old('academic_status', $alumni->academic_status?->value) == 'aktif' ? 'selected' : '' }}>Aktif (Mahasiswa)</option>
                        <option value="lulus" {{ old('academic_status', $alumni->academic_status?->value) == 'lulus' ? 'selected' : '' }}>Lulus (Alumni)</option>
                    </select>
                    @error('academic_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="card-body p-4 pt-0">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-1"></i>
                <strong>Catatan:</strong> Data pribadi (alamat, kontak, dll) dikelola sendiri oleh alumni melalui menu "Profil Saya".
                Data pekerjaan/pekerjaan ada di modul Tracer Study.
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card-footer py-3 d-flex justify-content-end gap-2" style="background: #f9fafb;">
            <a href="{{ route('admin.alumni.index') }}" class="btn btn-default">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
