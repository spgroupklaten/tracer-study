@extends('layouts.mahasiswa')

@section('title', 'Edit Kontak & Media Sosial')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Edit Kontak & Media Sosial</h1>
        <p class="text-muted small mb-0">Perbarui informasi kontak dan media sosial Anda</p>
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

    <form action="{{ route('mahasiswa.profile.contact.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="card mb-4">
            <div class="card-header py-2">
                <h6 class="mb-0 fw-bold">Kontak Utama</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="whatsapp" class="form-label small fw-medium">WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp text-success"></i></span>
                                <input type="text"
                                       class="form-control @error('whatsapp') is-invalid @enderror"
                                       id="whatsapp"
                                       name="whatsapp"
                                       value="{{ old('whatsapp', $contact->whatsapp ?? '') }}"
                                       placeholder="08xxxxxxxxxx">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text small">Contoh: 081234567890</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="website" class="form-label small fw-medium">Website / Portfolio</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                <input type="url"
                                       class="form-control @error('website') is-invalid @enderror"
                                       id="website"
                                       name="website"
                                       value="{{ old('website', $contact->website ?? '') }}"
                                       placeholder="https://portfolio.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header py-2">
                <h6 class="mb-0 fw-bold">Media Sosial</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- LinkedIn -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="linkedin" class="form-label small fw-medium">
                                <i class="fab fa-linkedin text-primary me-1"></i> LinkedIn
                            </label>
                            <input type="url"
                                   class="form-control @error('linkedin') is-invalid @enderror"
                                   id="linkedin"
                                   name="linkedin"
                                   value="{{ old('linkedin', $contact->linkedin ?? '') }}"
                                   placeholder="https://linkedin.com/in/username">
                            @error('linkedin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Instagram -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="instagram" class="form-label small fw-medium">
                                <i class="fab fa-instagram text-danger me-1"></i> Instagram
                            </label>
                            <input type="url"
                                   class="form-control @error('instagram') is-invalid @enderror"
                                   id="instagram"
                                   name="instagram"
                                   value="{{ old('instagram', $contact->instagram ?? '') }}"
                                   placeholder="https://instagram.com/username">
                            @error('instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Facebook -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="facebook" class="form-label small fw-medium">
                                <i class="fab fa-facebook text-primary me-1"></i> Facebook
                            </label>
                            <input type="url"
                                   class="form-control @error('facebook') is-invalid @enderror"
                                   id="facebook"
                                   name="facebook"
                                   value="{{ old('facebook', $contact->facebook ?? '') }}"
                                   placeholder="https://facebook.com/username">
                            @error('facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- X / Twitter -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="twitter" class="form-label small fw-medium">
                                <i class="fab fa-x-twitter me-1"></i> X / Twitter
                            </label>
                            <input type="url"
                                   class="form-control @error('twitter') is-invalid @enderror"
                                   id="twitter"
                                   name="twitter"
                                   value="{{ old('twitter', $contact->twitter ?? '') }}"
                                   placeholder="https://x.com/username">
                            @error('twitter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- GitHub -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="github" class="form-label small fw-medium">
                                <i class="fab fa-github me-1"></i> GitHub
                            </label>
                            <input type="url"
                                   class="form-control @error('github') is-invalid @enderror"
                                   id="github"
                                   name="github"
                                   value="{{ old('github', $contact->github ?? '') }}"
                                   placeholder="https://github.com/username">
                            @error('github')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- YouTube -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="youtube" class="form-label small fw-medium">
                                <i class="fab fa-youtube text-danger me-1"></i> YouTube
                            </label>
                            <input type="url"
                                   class="form-control @error('youtube') is-invalid @enderror"
                                   id="youtube"
                                   name="youtube"
                                   value="{{ old('youtube', $contact->youtube ?? '') }}"
                                   placeholder="https://youtube.com/@channel">
                            @error('youtube')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('mahasiswa.profile.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
