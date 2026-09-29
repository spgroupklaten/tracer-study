@extends('layouts.admin')

@section('title', 'Kuesioner Tracer Study')

@section('page-title', 'Kuesioner')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Kuesioner Tracer Study</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Kelola pertanyaan kuesioner tracer study</p>
    </div>
    <a href="{{ route('admin.kuesioner.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Pertanyaan
    </a>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.kuesioner.index') }}" class="row g-3 align-items-end">
            <!-- Search -->
            <div class="col-12 col-md-3">
                <label class="form-label">Cari</label>
                <div class="position-relative">
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari pertanyaan..."
                           class="form-control" style="padding-left: 2rem;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                </div>
            </div>

            <!-- Type Filter -->
            <div class="col-6 col-md-2">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-control form-select">
                    <option value="">Semua</option>
                    @foreach(['text', 'textarea', 'number', 'radio', 'select', 'checkbox', 'date'] as $type)
                        <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-6 col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-control form-select">
                    <option value="">Semua</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('admin.kuesioner.index') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Questions Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 60px;">Urutan</th>
                        <th>Pertanyaan</th>
                        <th style="width: 100px;">Tipe</th>
                        <th style="width: 70px; text-align: center;">Wajib</th>
                        <th style="width: 80px; text-align: center;">Status</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $index => $question)
                        <tr>
                            <td class="text-muted" style="font-size: 0.8125rem;">
                                {{ $questions->firstItem() + $index }}
                            </td>
                            <td style="font-weight: 500;">{{ $question->order }}</td>
                            <td>
                                <div style="font-weight: 500; color: #374151;">
                                    {{ Str::limit($question->question_text, 60) }}
                                </div>
                                @if($question->code)
                                    <div class="text-muted" style="font-size: 0.75rem;">Kode: {{ $question->code }}</div>
                                @endif
                                @if($question->isChoiceType())
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $question->questionOptions->count() }} pilihan
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $question->type->label() }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($question->is_required)
                                    <span class="badge bg-danger">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($question->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.kuesioner.edit', $question) }}" class="btn btn-xs btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.kuesioner.toggle-status', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-xs {{ $question->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $question->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas {{ $question->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.kuesioner.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
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
                                    <i class="fas fa-question-circle text-4xl mb-3 d-block" style="color: #d1d5db;"></i>
                                    <p class="mb-1" style="font-weight: 500; color: #374151;">Belum ada pertanyaan</p>
                                    <p class="mb-3" style="font-size: 0.875rem;">Mulai dengan menambahkan pertanyaan baru.</p>
                                    <a href="{{ route('admin.kuesioner.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Tambah Pertanyaan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size: 0.8125rem;">
                Menampilkan {{ $questions->firstItem() }} - {{ $questions->lastItem() }} dari {{ $questions->total() }} data
            </div>
            <div>
                {{ $questions->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4 mb-0" role="alert">
    <div class="d-flex align-items-start gap-2">
        <i class="fas fa-info-circle mt-1"></i>
        <div style="font-size: 0.875rem;">
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li>Pertanyaan yang sudah memiliki jawaban tidak dapat dihapus.</li>
                <li>Pertanyaan nonaktif tidak akan ditampilkan pada form mahasiswa.</li>
                <li>Urutan pertanyaan menentukan urutan tampil pada form.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
