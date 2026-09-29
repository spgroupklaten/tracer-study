@extends('layouts.mahasiswa')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Edit Profil</h1>
        <p class="text-muted small mb-0">Perbarui informasi data pribadi Anda</p>
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

    <form action="{{ route('mahasiswa.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-4">
                <!-- Profile Photo -->
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0 fw-bold">Foto Profil</h6>
                    </div>
                    <div class="card-body text-center">
                        <!-- Current Photo -->
                        <div class="mb-3">
                            @if($profile->foto && Storage::disk('public')->exists($profile->foto))
                                <img src="{{ Storage::url($profile->foto) }}"
                                     alt="{{ $user->name }}"
                                     class="rounded-circle shadow-sm"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="current-photo">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                     style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                                     id="current-photo">
                                    <span class="text-white fw-bold" style="font-size: 3rem;">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Input -->
                        <div class="mb-2">
                            <input type="file"
                                   class="form-control form-control-sm @error('foto') is-invalid @enderror"
                                   name="foto"
                                   id="foto-input"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   onchange="previewPhoto(this)">
                            @error('foto')
                                <div class="invalid-feedback text-start">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Format: JPG, PNG, WEBP. Maks: 2MB</div>
                        </div>

                        <!-- Preview Container -->
                        <div id="photo-preview" class="d-none">
                            <img id="preview-img" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removePhoto()">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Forms -->
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0 fw-bold">Nama Lengkap</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-medium">Nama Lengkap</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Data -->
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <h6 class="mb-0 fw-bold">Data Pribadi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nik" class="form-label small fw-medium">NIK (Nomor Induk Kependudukan)</label>
                                    <input type="text"
                                           class="form-control @error('nik') is-invalid @enderror"
                                           id="nik"
                                           name="nik"
                                           value="{{ old('nik', $profile->nik ?? '') }}"
                                           maxlength="16">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label small fw-medium">Jenis Kelamin</label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin"
                                            name="jenis_kelamin">
                                        <option value="">Pilih</option>
                                        <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tempat_lahir" class="form-label small fw-medium">Tempat Lahir</label>
                                    <input type="text"
                                           class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           id="tempat_lahir"
                                           name="tempat_lahir"
                                           value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label small fw-medium">Tanggal Lahir</label>
                                    <input type="date"
                                           class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                           id="tanggal_lahir"
                                           name="tanggal_lahir"
                                           value="{{ old('tanggal_lahir', $profile->tanggal_lahir ? $profile->tanggal_lahir->format('Y-m-d') : '') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status_pernikahan" class="form-label small fw-medium">Status Pernikahan</label>
                                    <select class="form-select @error('status_pernikahan') is-invalid @enderror"
                                            id="status_pernikahan"
                                            name="status_pernikahan">
                                        <option value="">Pilih</option>
                                        <option value="belum_menikah" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="menikah" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="cerai_hidup" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="cerai_mati" {{ old('status_pernikahan', $profile->status_pernikahan ?? '') == 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </select>
                                    @error('status_pernikahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="agama" class="form-label small fw-medium">Agama</label>
                                    <select class="form-select @error('agama') is-invalid @enderror"
                                            id="agama"
                                            name="agama">
                                        <option value="">Pilih</option>
                                        <option value="islam" {{ old('agama', $profile->agama ?? '') == 'islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="kristen" {{ old('agama', $profile->agama ?? '') == 'kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="katolik" {{ old('agama', $profile->agama ?? '') == 'katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="hindu" {{ old('agama', $profile->agama ?? '') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="buddha" {{ old('agama', $profile->agama ?? '') == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="konghucu" {{ old('agama', $profile->agama ?? '') == 'konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        <option value="lainnya" {{ old('agama', $profile->agama ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="domisili" class="form-label small fw-medium">Domisili</label>
                                    <input type="text"
                                           class="form-control @error('domisili') is-invalid @enderror"
                                           id="domisili"
                                           name="domisili"
                                           value="{{ old('domisili', $profile->domisili ?? '') }}"
                                           placeholder="Contoh: Klaten">
                                    @error('domisili')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="no_hp" class="form-label small fw-medium">Nomor HP</label>
                                    <input type="text"
                                           class="form-control @error('no_hp') is-invalid @enderror"
                                           id="no_hp"
                                           name="no_hp"
                                           value="{{ old('no_hp', $profile->no_hp ?? '') }}"
                                           placeholder="Contoh: 081234567890">
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="alamat_ktp" class="form-label small fw-medium">Alamat Sesuai KTP</label>
                                    <textarea class="form-control @error('alamat_ktp') is-invalid @enderror"
                                              id="alamat_ktp"
                                              name="alamat_ktp"
                                              rows="2">{{ old('alamat_ktp', $profile->alamat_ktp ?? '') }}</textarea>
                                    @error('alamat_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="alamat_tinggal" class="form-label small fw-medium">Alamat Tinggal</label>
                                    <textarea class="form-control @error('alamat_tinggal') is-invalid @enderror"
                                              id="alamat_tinggal"
                                              name="alamat_tinggal"
                                              rows="2">{{ old('alamat_tinggal', $profile->alamat_tinggal ?? '') }}</textarea>
                                    @error('alamat_tinggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="hobi" class="form-label small fw-medium">Hobi</label>
                                    <input type="text"
                                           class="form-control @error('hobi') is-invalid @enderror"
                                           id="hobi"
                                           name="hobi"
                                           value="{{ old('hobi', $profile->hobi ?? '') }}"
                                           placeholder="Contoh: Membaca, Olahraga">
                                    @error('hobi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label small fw-medium">Alamat Lengkap</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror"
                                              id="alamat"
                                              name="alamat"
                                              rows="2">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="kelurahan" class="form-label small fw-medium">Kelurahan</label>
                                            <input type="text"
                                                   class="form-control @error('kelurahan') is-invalid @enderror"
                                                   id="kelurahan"
                                                   name="kelurahan"
                                                   value="{{ old('kelurahan', $profile->kelurahan ?? '') }}">
                                            @error('kelurahan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="kecamatan" class="form-label small fw-medium">Kecamatan</label>
                                            <input type="text"
                                                   class="form-control @error('kecamatan') is-invalid @enderror"
                                                   id="kecamatan"
                                                   name="kecamatan"
                                                   value="{{ old('kecamatan', $profile->kecamatan ?? '') }}">
                                            @error('kecamatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="kabupaten" class="form-label small fw-medium">Kabupaten/Kota</label>
                                            <input type="text"
                                                   class="form-control @error('kabupaten') is-invalid @enderror"
                                                   id="kabupaten"
                                                   name="kabupaten"
                                                   value="{{ old('kabupaten', $profile->kabupaten ?? '') }}">
                                            @error('kabupaten')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="provinsi" class="form-label small fw-medium">Provinsi</label>
                                            <input type="text"
                                                   class="form-control @error('provinsi') is-invalid @enderror"
                                                   id="provinsi"
                                                   name="provinsi"
                                                   value="{{ old('provinsi', $profile->provinsi ?? '') }}">
                                            @error('provinsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="kode_pos" class="form-label small fw-medium">Kode Pos</label>
                                    <input type="text"
                                           class="form-control @error('kode_pos') is-invalid @enderror"
                                           id="kode_pos"
                                           name="kode_pos"
                                           value="{{ old('kode_pos', $profile->kode_pos ?? '') }}"
                                           maxlength="10">
                                    @error('kode_pos')
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
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photo-preview');
                const previewImg = document.getElementById('preview-img');
                const currentPhoto = document.getElementById('current-photo');

                previewImg.src = e.target.result;
                preview.classList.remove('d-none');
                if (currentPhoto) {
                    currentPhoto.classList.add('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePhoto() {
        const input = document.getElementById('foto-input');
        const preview = document.getElementById('photo-preview');
        const currentPhoto = document.getElementById('current-photo');

        input.value = '';
        preview.classList.add('d-none');
        if (currentPhoto) {
            currentPhoto.classList.remove('d-none');
        }
    }
</script>
@endpush
@endsection
