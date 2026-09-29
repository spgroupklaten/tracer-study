@extends('layouts.mahasiswa')

@section('title', 'Edit Skill')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Edit Skill</h1>
        <p class="text-muted small mb-0">Perbarui level dan deskripsi skill Anda</p>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.profile.index') }}">Profil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mahasiswa.profile.index') }}">Skill</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('mahasiswa.profile.skills.update', $skill->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <!-- Skill Name (read-only) -->
                <div class="mb-3">
                    <label class="form-label small fw-medium">Skill</label>
                    <input type="text" class="form-control" value="{{ $skill->name }}" readonly>
                    <div class="form-text">Nama skill tidak dapat diubah. Hubungi administrator untuk mengubah master skill.</div>
                </div>

                <!-- Level -->
                <div class="mb-3">
                    <label for="level" class="form-label small fw-medium">Level <span class="text-danger">*</span></label>
                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                        <option value="">Pilih Level</option>
                        @foreach($skillLevels as $value => $label)
                            <option value="{{ $value }}" {{ old('level', $pivot->level) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label small fw-medium">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Opsional: Tambahkan deskripsi skill Anda">{{ old('description', $pivot->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Jelaskan pengalaman atau detail skill Anda.</div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <form action="{{ route('mahasiswa.profile.skills.destroy', $skill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skill ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i> Hapus Skill
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
