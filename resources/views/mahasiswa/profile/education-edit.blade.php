@extends('layouts.mahasiswa')

@section('title', 'Edit Riwayat Pendidikan')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Edit Riwayat Pendidikan</h1>
        <p class="text-muted small mb-0">Perbarui informasi pendidikan Anda</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('mahasiswa.profile.education.update', $education->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="card mb-4">
            <div class="card-header py-2">
                <h6 class="mb-0 fw-bold">Data Pendidikan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenjang" class="form-label small fw-medium">Jenjang <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenjang') is-invalid @enderror" id="jenjang" name="jenjang" required>
                                <option value="">Pilih Jenjang</option>
                                @foreach(App\Enums\EducationLevel::options() as $value => $label)
                                    <option value="{{ $value }}" {{ old('jenjang', $education->jenjang) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenjang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_institusi" class="form-label small fw-medium">Nama Institusi <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nama_institusi') is-invalid @enderror"
                                   id="nama_institusi"
                                   name="nama_institusi"
                                   value="{{ old('nama_institusi', $education->nama_institusi) }}"
                                   required>
                            @error('nama_institusi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jurusan" class="form-label small fw-medium">Jurusan / Program Studi</label>
                            <input type="text"
                                   class="form-control @error('jurusan') is-invalid @enderror"
                                   id="jurusan"
                                   name="jurusan"
                                   value="{{ old('jurusan', $education->jurusan) }}">
                            @error('jurusan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kota" class="form-label small fw-medium">Kota</label>
                            <input type="text"
                                   class="form-control @error('kota') is-invalid @enderror"
                                   id="kota"
                                   name="kota"
                                   value="{{ old('kota', $education->kota) }}">
                            @error('kota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahun_masuk" class="form-label small fw-medium">Tahun Masuk <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('tahun_masuk') is-invalid @enderror"
                                   id="tahun_masuk"
                                   name="tahun_masuk"
                                   value="{{ old('tahun_masuk', $education->tahun_masuk) }}"
                                   min="1900"
                                   max="{{ date('Y') + 5 }}"
                                   required>
                            @error('tahun_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tahun_lulus" class="form-label small fw-medium">Tahun Lulus</label>
                            <input type="number"
                                   class="form-control @error('tahun_lulus') is-invalid @enderror"
                                   id="tahun_lulus"
                                   name="tahun_lulus"
                                   value="{{ old('tahun_lulus', $education->tahun_lulus) }}"
                                   min="1900"
                                   max="{{ date('Y') + 5 }}">
                            @error('tahun_lulus')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label small fw-medium">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan"
                              name="keterangan"
                              rows="2">{{ old('keterangan', $education->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('mahasiswa.profile.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <div class="d-flex gap-2">
                <form action="{{ route('mahasiswa.profile.education.destroy', $education->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pendidikan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </form>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
