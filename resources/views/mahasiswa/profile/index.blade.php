@extends('layouts.mahasiswa')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-1">Profil Saya</h1>
        <p class="text-muted small mb-0">Kelola informasi pribadi dan data akademik Anda</p>
    </div>

    @if($isNew)
        <!-- No Profile Warning -->
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div>
                <strong>Profil belum lengkap.</strong> Silakan lengkapi data pribadi Anda.
                <a href="{{ route('mahasiswa.profile.edit') }}" class="alert-link">Klik di sini untuk melengkapi.</a>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Profile Card with Photo -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    <!-- Profile Photo -->
                    <div class="mb-3">
                        @if($profile && $profile->foto && Storage::disk('public')->exists($profile->foto))
                            <img src="{{ Storage::url($profile->foto) }}"
                                 alt="{{ $user->name }}"
                                 class="rounded-circle shadow-sm"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                 style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <span class="text-white fw-bold" style="font-size: 2.5rem;">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Name & Status -->
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="badge mb-2" style="background-color: {{ $profile && $profile->isLulus() ? '#6c757d' : '#198754' }}; color: white;">
                        {{ $profile ? $profile->academicStatusLabel : 'Mahasiswa' }}
                    </p>

                    @if($profile)
                        <p class="text-muted small mb-0">
                            <i class="fas fa-id-card me-1"></i>
                            {{ $profile->nim ?? 'NIM belum diisi' }}
                        </p>
                    @endif

                    <!-- Edit Button -->
                    <div class="mt-3">
                        <a href="{{ route('mahasiswa.profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 fw-bold">Informasi Akun</h6>
                </div>
                <div class="card-body py-2">
                    <p class="small text-muted mb-1">
                        <i class="fas fa-envelope me-2 text-muted"></i>
                        {{ $user->email }}
                    </p>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-user-tag me-2 text-muted"></i>
                        {{ ucfirst($user->role->value) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="col-md-8">
            <!-- Academic Data -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Data Akademik</h6>
                    <span class="badge bg-secondary-subtle text-secondary small">Dikelola Administrator</span>
                </div>
                <div class="card-body">
                    @if($profile && $profile->nim)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">NIM</td>
                                        <td class="small">{{ $profile->nim }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Fakultas</td>
                                        <td class="small">{{ $profile->fakultas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Program Studi</td>
                                        <td class="small">{{ $profile->program_studi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Angkatan</td>
                                        <td class="small">{{ $profile->angkatan ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">Tahun Masuk</td>
                                        <td class="small">{{ $profile->tahun_masuk ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Tahun Lulus</td>
                                        <td class="small">{{ $profile->tahun_lulus ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Tanggal Lulus</td>
                                        <td class="small">{{ $profile->tanggal_lulus ? $profile->tanggal_lulus->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">IPK</td>
                                        <td class="small">{{ $profile->ipk ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Data akademik belum tersedia. Silakan hubungi administrator.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Personal Data -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Data Pribadi</h6>
                    <a href="{{ route('mahasiswa.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    @if($profile)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">NIK</td>
                                        <td class="small">{{ $profile->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Jenis Kelamin</td>
                                        <td class="small">{{ $profile->jenisKelaminDisplay }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Tempat Lahir</td>
                                        <td class="small">{{ $profile->tempat_lahir ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Tanggal Lahir</td>
                                        <td class="small">{{ $profile->tanggal_lahir ? $profile->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Status Pernikahan</td>
                                        <td class="small">{{ $profile->status_pernikahan ? App\Helpers\EnumHelper::statusPernikahan($profile->status_pernikahan) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Agama</td>
                                        <td class="small">{{ $profile->agama ? App\Helpers\EnumHelper::agama($profile->agama) : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">Domisili</td>
                                        <td class="small">{{ $profile->domisili ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Nomor HP</td>
                                        <td class="small">{{ $profile->no_hp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Alamat KTP</td>
                                        <td class="small">{{ $profile->alamat_ktp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Alamat Tinggal</td>
                                        <td class="small">{{ $profile->alamat_tinggal ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Hobi</td>
                                        <td class="small">{{ $profile->hobi ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if($profile->alamat)
                            <hr class="my-2">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted small fw-medium" style="width: 140px;">Alamat Lengkap</td>
                                    <td class="small">
                                        {{ $profile->alamat }}
                                        @if($profile->kelurahan || $profile->kecamatan || $profile->kabupaten || $profile->provinsi)
                                            , {{ collect([$profile->kelurahan, $profile->kecamatan, $profile->kabupaten, $profile->provinsi])->filter()->implode(', ') }}
                                        @endif
                                        {{ $profile->kode_pos ? $profile->kode_pos : '' }}
                                    </td>
                                </tr>
                            </table>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-circle me-1"></i>
                            Data pribadi belum tersedia. Silakan lengkapi data Anda.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact & Social Media -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Kontak & Media Sosial</h6>
                    <a href="{{ route('mahasiswa.profile.contact.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    @if($profile && $profile->contact)
                        @php
                            $contact = $profile->contact;
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">WhatsApp</td>
                                        <td class="small">
                                            @if($contact->whatsapp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}" target="_blank" rel="noopener noreferrer">{{ $contact->whatsapp }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Website</td>
                                        <td class="small">
                                            @if($contact->website)
                                                <a href="{{ $contact->website }}" target="_blank" rel="noopener noreferrer">{{ Str::limit($contact->website, 30) }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">LinkedIn</td>
                                        <td class="small">
                                            @if($contact->linkedin)
                                                <a href="{{ $contact->linkedin }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin text-primary me-1"></i> LinkedIn</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">Instagram</td>
                                        <td class="small">
                                            @if($contact->instagram)
                                                <a href="{{ $contact->instagram }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram text-danger me-1"></i> Instagram</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted small fw-medium" style="width: 140px;">Facebook</td>
                                        <td class="small">
                                            @if($contact->facebook)
                                                <a href="{{ $contact->facebook }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook text-primary me-1"></i> Facebook</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">X / Twitter</td>
                                        <td class="small">
                                            @if($contact->twitter)
                                                <a href="{{ $contact->twitter }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-x-twitter text-dark me-1"></i> Twitter</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">GitHub</td>
                                        <td class="small">
                                            @if($contact->github)
                                                <a href="{{ $contact->github }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-github text-dark me-1"></i> GitHub</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-medium">YouTube</td>
                                        <td class="small">
                                            @if($contact->youtube)
                                                <a href="{{ $contact->youtube }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube text-danger me-1"></i> YouTube</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-share-alt me-1"></i>
                            Kontak dan media sosial belum diisi.
                            <a href="{{ route('mahasiswa.profile.contact.edit') }}" class="text-primary">Tambah sekarang</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Education History -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Riwayat Pendidikan</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    @if($profile && $profile->educationHistories->count() > 0)
                        <div class="row">
                            @foreach($profile->educationHistoriesSorted as $education)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge bg-primary mb-2">{{ $education->jenjang_label }}</span>
                                                <h6 class="mb-1">{{ $education->nama_institusi }}</h6>
                                                @if($education->jurusan)
                                                    <p class="small text-muted mb-1">{{ $education->jurusan }}</p>
                                                @endif
                                                <p class="small text-muted mb-0">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $education->tahun_range }}
                                                    @if($education->kota)
                                                        <span class="mx-1">•</span>
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $education->kota }}
                                                    @endif
                                                </p>
                                                @if($education->keterangan)
                                                    <p class="small text-muted mb-0 mt-1 fst-italic">{{ $education->keterangan }}</p>
                                                @endif
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('mahasiswa.profile.education.edit', $education->id) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('mahasiswa.profile.education.destroy', $education->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pendidikan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-graduation-cap me-1"></i>
                            Tidak ada riwayat pendidikan.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Skills -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Keahlian / Skill</h6>
                    @if($profile && $skills->count() < 20)
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($skills) && $skills->count() > 0)
                        <div class="row">
                            @foreach($skills as $skill)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $skill->name }}</h6>
                                                <span class="badge bg-info mb-2">{{ $skill->pivot->level_label }}</span>
                                                @if($skill->pivot->description)
                                                    <p class="small text-muted mb-0">{{ $skill->pivot->description }}</p>
                                                @endif
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('mahasiswa.profile.skills.edit', $skill->id) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('mahasiswa.profile.skills.destroy', $skill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skill ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-tools me-1"></i>
                            Belum ada skill yang ditambahkan.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Achievements -->
            <div class="card mb-4">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Prestasi</h6>
                    @if($profile)
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAchievementModal">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($achievements) && $achievements->count() > 0)
                        <div class="row">
                            @foreach($achievements as $achievement)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $achievement->judul }}</h6>
                                                <div class="mb-2">
                                                    <span class="badge bg-{{ $achievement->jenis === 'akademik' ? 'primary' : 'success' }} me-1">{{ $achievement->jenis_label }}</span>
                                                    <span class="badge bg-secondary">{{ $achievement->tingkat_label }}</span>
                                                </div>
                                                @if($achievement->penyelenggara)
                                                    <p class="small text-muted mb-1">
                                                        <i class="fas fa-building me-1"></i>{{ $achievement->penyelenggara }}
                                                    </p>
                                                @endif
                                                @if($achievement->tahun)
                                                    <p class="small text-muted mb-1">
                                                        <i class="fas fa-calendar me-1"></i>{{ $achievement->tahun }}
                                                    </p>
                                                @endif
                                                @if($achievement->deskripsi)
                                                    <p class="small text-muted mb-0">{{ Str::limit($achievement->deskripsi, 100) }}</p>
                                                @endif
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                @if($achievement->hasCertificate())
                                                    <a href="{{ Storage::url($achievement->certificate_path) }}" target="_blank" class="btn btn-outline-info" title="Lihat Sertifikat">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('mahasiswa.profile.achievements.edit', $achievement->id) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('mahasiswa.profile.achievements.destroy', $achievement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-trophy me-1"></i>
                            Belum ada prestasi yang ditambahkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Education Modal -->
@if($profile)
<div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEducationModalLabel">Tambah Riwayat Pendidikan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mahasiswa.profile.education.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenjang" class="form-label small fw-medium">Jenjang <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenjang') is-invalid @enderror" id="jenjang" name="jenjang" required>
                                    <option value="">Pilih Jenjang</option>
                                    @foreach(App\Enums\EducationLevel::options() as $value => $label)
                                        <option value="{{ $value }}" {{ old('jenjang') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                <input type="text" class="form-control @error('nama_institusi') is-invalid @enderror" id="nama_institusi" name="nama_institusi" value="{{ old('nama_institusi') }}" required>
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
                                <input type="text" class="form-control @error('jurusan') is-invalid @enderror" id="jurusan" name="jurusan" value="{{ old('jurusan') }}">
                                @error('jurusan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kota" class="form-label small fw-medium">Kota</label>
                                <input type="text" class="form-control @error('kota') is-invalid @enderror" id="kota" name="kota" value="{{ old('kota') }}">
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
                                <input type="number" class="form-control @error('tahun_masuk') is-invalid @enderror" id="tahun_masuk" name="tahun_masuk" value="{{ old('tahun_masuk') }}" min="1900" max="{{ date('Y') + 5 }}" required>
                                @error('tahun_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tahun_lulus" class="form-label small fw-medium">Tahun Lulus</label>
                                <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus') }}" min="1900" max="{{ date('Y') + 5 }}">
                                @error('tahun_lulus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label small fw-medium">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Add Skill Modal -->
@if($profile)
<div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkillModalLabel">Tambah Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mahasiswa.profile.skills.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="skill_id" class="form-label small fw-medium">Skill <span class="text-danger">*</span></label>
                        <select class="form-select @error('skill_id') is-invalid @enderror" id="skill_id" name="skill_id" required>
                            <option value="">Pilih Skill</option>
                            @foreach(\App\Models\Skill::orderBy('name')->get() as $skillOption)
                                <option value="{{ $skillOption->id }}" {{ old('skill_id') == $skillOption->id ? 'selected' : '' }}>{{ $skillOption->name }}</option>
                            @endforeach
                        </select>
                        @error('skill_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label small fw-medium">Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                            <option value="">Pilih Level</option>
                            @foreach($skillLevels ?? App\Enums\SkillLevel::options() as $value => $label)
                                <option value="{{ $value }}" {{ old('level') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label small fw-medium">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Opsional: Tambahkan deskripsi skill Anda">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Add Achievement Modal -->
@if($profile)
<div class="modal fade" id="addAchievementModal" tabindex="-1" aria-labelledby="addAchievementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAchievementModalLabel">Tambah Prestasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mahasiswa.profile.achievements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="judul" class="form-label small fw-medium">Judul Prestasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Juara 1 Lomba Programming" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tahun" class="form-label small fw-medium">Tahun</label>
                                <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun') }}" min="1900" max="{{ date('Y') }}">
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
                                    @foreach(App\Enums\AchievementType::options() as $value => $label)
                                        <option value="{{ $value }}" {{ old('jenis') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                    @foreach(App\Enums\AchievementLevel::options() as $value => $label)
                                        <option value="{{ $value }}" {{ old('tingkat') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                                <input type="text" class="form-control @error('penyelenggara') is-invalid @enderror" id="penyelenggara" name="penyelenggara" value="{{ old('penyelenggara') }}" placeholder="Contoh: Universitas ABC">
                                @error('penyelenggara')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label small fw-medium">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal') }}">
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label small fw-medium">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="2">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="certificate" class="form-label small fw-medium">Sertifikat</label>
                        <input type="file" class="form-control @error('certificate') is-invalid @enderror" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        @error('certificate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: PDF, JPG, JPEG, PNG, WEBP. Maksimal 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
