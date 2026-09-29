@extends('layouts.mahasiswa')

@section('title', 'Edit Prestasi')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Edit Prestasi</h1>
        <p class="text-muted small mb-0">Perbarui informasi prestasi Anda</p>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.profile.index') }}">Profil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.profile.index') }}#prestasis">Prestasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('mahasiswa.profile.achievements.update', $achievement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="judul" class="form-label small fw-medium">Judul Prestasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $achievement->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tahun" class="form-label small fw-medium">Tahun</label>
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun', $achievement->tahun) }}" min="1900" max="{{ date('Y') }}">
                            @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis" class="form-label small fw-medium">Jenis <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis') is-invalid @enderror" id="jenis" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                @foreach($achievementTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenis', $achievement->jenis->value) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tingkat" class="form-label small fw-medium">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select @error('tingkat') is-invalid @enderror" id="tingkat" name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                @foreach($achievementLevels as $value => $label)
                                    <option value="{{ $value }}" {{ old('tingkat', $achievement->tingkat->value) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tingkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="penyelenggara" class="form-label small fw-medium">Penyelenggara</label>
                            <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara', $achievement->penyelenggara) }}">
                            @error('penyelenggara')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label small fw-medium">Tanggal</label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $achievement->tanggal?->format('Y-m-d')) }}">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label small fw-medium">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $achievement->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Certificate -->
                <div class="mb-3">
                    <label for="certificate" class="form-label small fw-medium">Sertifikat</label>

                    @if($achievement->hasCertificate())
                        <div class="mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-alt text-success me-2"></i>
                                <span class="small">{{ $achievement->certificate_original_name }}</span>
                                <a href="{{ Storage::url($achievement->certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="remove_certificate" name="remove_certificate" value="1">
                            <label class="form-check-label small text-danger" for="remove_certificate">
                                Hapus sertifikat yang ada
                            </label>
                        </div>
                    @endif

                    <input type="file" class="form-control @error('certificate') is-invalid @enderror" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    @error('certificate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        @if($achievement->hasCertificate())
                            Upload file baru untuk mengganti sertifikat yang ada.
                        @endif
                        Format: PDF, JPG, JPEG, PNG, WEBP. Maksimal 5MB.
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <form action="{{ route('mahasiswa.profile.achievements.destroy', $achievement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini? Sertifikat juga akan dihapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </form>
                    <div class="d-flex gap-2">
                        <a href="{{ route('mahasiswa.profile.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
