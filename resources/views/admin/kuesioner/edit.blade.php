@extends('layouts.admin')

@section('title', 'Edit Pertanyaan')

@section('page-title', 'Kuesioner')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Edit Pertanyaan</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Edit pertanyaan kuesioner tracer study</p>
    </div>
    <a href="{{ route('admin.kuesioner.index') }}" class="btn btn-sm btn-default">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Form -->
<div class="card" style="max-width: 42rem;">
    <form action="{{ route('admin.kuesioner.update', $question) }}" method="POST" id="question-form" class="card-body p-4">
        @csrf
        @method('PUT')

        <!-- Info Box -->
        @if($question->answers()->exists())
            <div class="alert alert-warning py-2 mb-4">
                <div class="d-flex gap-2">
                    <i class="fas fa-exclamation-triangle mt-1"></i>
                    <div>
                        <h6 class="mb-1" style="font-size: 0.875rem;">Perhatian</h6>
                        <p class="mb-0" style="font-size: 0.8125rem;">Pertanyaan ini sudah memiliki {{ $question->answers()->count() }} jawaban.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger py-2 mb-4">
                <div class="d-flex gap-2">
                    <i class="fas fa-exclamation-circle mt-1"></i>
                    <div>
                        <h6 class="mb-1" style="font-size: 0.875rem;">Mohon perbaiki kesalahan berikut:</h6>
                        <ul class="mb-0 ps-3" style="font-size: 0.8125rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Question Text -->
        <div class="mb-3">
            <label for="question_text" class="form-label" style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                Pertanyaan <span class="text-danger">*</span>
            </label>
            <textarea id="question_text"
                      name="question_text"
                      rows="3"
                      required
                      maxlength="500"
                      class="form-control @error('question_text') is-invalid @enderror"
                      placeholder="Tulis pertanyaan di sini...">{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <!-- Type -->
        <div class="mb-3">
            <label for="type" class="form-label" style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                Tipe Pertanyaan <span class="text-danger">*</span>
            </label>
            <select id="type"
                    name="type"
                    required
                    class="form-select @error('type') is-invalid @enderror">
                <option value="">-- Pilih Tipe --</option>
                @foreach(['text', 'textarea', 'number', 'radio', 'select', 'checkbox', 'date'] as $type)
                    <option value="{{ $type }}" {{ old('type', $question->type->value) === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dynamic Options -->
        <div class="mb-3" id="options-container" style="display: none;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label mb-0" style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                    Pilihan Jawaban <span class="text-danger">*</span>
                </label>
                <button type="button" id="add-option-btn" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah
                </button>
            </div>
            <div id="options-list" class="space-y-2"></div>
            <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">Label adalah teks yang ditampilkan, Value adalah nilai yang disimpan.</p>
        </div>

        <!-- Code -->
        <div class="mb-3">
            <label for="code" class="form-label" style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                Kode Pertanyaan
            </label>
            <input type="text" id="code" name="code" value="{{ old('code', $question->code) }}"
                   maxlength="50"
                   class="form-control @error('code') is-invalid @enderror"
                   placeholder="status_saat_ini">
            <p class="text-muted mb-0 mt-1" style="font-size: 0.75rem;">Kode unik untuk identifikasi internal (opsional)</p>
        </div>

        <!-- Order -->
        <div class="mb-3">
            <label for="order" class="form-label" style="font-size: 0.875rem; font-weight: 500; color: #374151;">
                Urutan <span class="text-danger">*</span>
            </label>
            <input type="number" id="order" name="order" value="{{ old('order', $question->order) }}"
                   required min="0" max="9999"
                   class="form-control @error('order') is-invalid @enderror">
        </div>

        <!-- Checkboxes -->
        <div class="row g-2 mb-4">
            <div class="col-6">
                <label class="form-check p-3 border rounded d-block" style="cursor: pointer;">
                    <input id="is_required" name="is_required" type="checkbox" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }}
                           class="form-check-input me-2">
                    <div>
                        <span class="d-block" style="font-size: 0.875rem; font-weight: 500; color: #374151;">Pertanyaan Wajib</span>
                        <span class="d-block text-muted" style="font-size: 0.75rem;">Mahasiswa harus menjawab</span>
                    </div>
                </label>
            </div>
            <div class="col-6">
                <label class="form-check p-3 border rounded d-block" style="cursor: pointer;">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}
                           class="form-check-input me-2">
                    <div>
                        <span class="d-block" style="font-size: 0.875rem; font-weight: 500; color: #374151;">Aktif</span>
                        <span class="d-block text-muted" style="font-size: 0.75rem;">Ditampilkan pada form</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-end gap-2 pt-3" style="border-top: 1px solid #e5e7eb;">
            <a href="{{ route('admin.kuesioner.index') }}" class="btn btn-default">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('options-container');
    const optionsList = document.getElementById('options-list');
    const addOptionBtn = document.getElementById('add-option-btn');

    const existingOptions = {!! json_encode($question->questionOptions->map(fn($opt) => [
        'id' => $opt->id,
        'label' => $opt->label,
        'value' => $opt->value,
        'has_answers' => $opt->answers()->exists()
    ])) !!};
    const hasAnswers = {{ $question->answers()->exists() ? 'true' : 'false' }};

    function addOption(id, label, value, hasAns) {
        const div = document.createElement('div');
        div.className = 'option-item d-flex align-items-center gap-2 p-2 rounded';
        div.style.background = '#f9fafb';
        const hasAnsAttr = hasAns ? 'disabled style="opacity:0.5"' : '';
        div.innerHTML = `
            <input type="hidden" name="option_ids[]" value="${id || ''}">
            <div class="flex-grow-1 row g-2">
                <div class="col-6">
                    <input type="text" name="option_labels[]" value="${label}" placeholder="Label" class="form-control form-control-sm">
                </div>
                <div class="col-6">
                    <input type="text" name="option_values[]" value="${value}" placeholder="Value" class="form-control form-control-sm">
                </div>
            </div>
            <button type="button" class="remove-option-btn btn btn-sm btn-outline-danger" ${hasAns ? 'disabled' : ''}>
                <i class="fas fa-times"></i>
            </button>
        `;

        if (!hasAns) {
            div.querySelector('.remove-option-btn').addEventListener('click', function() {
                if (optionsList.children.length > 1) div.remove();
            });
        }
        optionsList.appendChild(div);
    }

    function toggleOptions() {
        const choiceTypes = ['radio', 'select', 'checkbox'];
        if (choiceTypes.includes(typeSelect.value)) {
            optionsContainer.style.display = 'block';
            if (optionsList.children.length === 0) {
                if (existingOptions.length > 0) {
                    existingOptions.forEach(opt => addOption(opt.id, opt.label, opt.value, opt.has_answers));
                } else {
                    addOption(null, '', '', false);
                }
            }
        } else {
            optionsContainer.style.display = 'none';
        }
    }

    typeSelect.addEventListener('change', toggleOptions);
    addOptionBtn.addEventListener('click', function() { addOption(null, '', '', false); });

    toggleOptions();
});
</script>
@endpush
