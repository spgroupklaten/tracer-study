@extends('layouts.admin')

@section('title', 'Statistik Jawaban Kuesioner')

@section('page-title', 'Statistik')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #111827;">Statistik Jawaban Kuesioner</h1>
        <p class="mb-0 text-muted" style="font-size: 0.8125rem;">Statistik berdasarkan data jawaban yang tersimpan</p>
    </div>
    <a href="{{ route('admin.statistics.tracer-study') }}" class="btn btn-sm btn-default">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Total Submitted</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #4f46e5;">{{ $summary['total_submitted_responses'] }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #e0e7ff;">
                    <i class="fas fa-clipboard-check" style="color: #4f46e5;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Total Pertanyaan</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $summary['total_questions'] }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #f3f4f6;">
                    <i class="fas fa-question-circle" style="color: #6b7280;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1" style="font-size: 0.75rem;">Pertanyaan Aktif</p>
                    <h3 class="mb-0" style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ $summary['total_active_questions'] }}</h3>
                </div>
                <div class="rounded d-flex align-items-center justify-content-center" style="width: 2.5rem; height: 2.5rem; background: #dcfce7;">
                    <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty State -->
@if($summary['total_submitted_responses'] == 0)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
            <h5 class="mb-2" style="color: #374151;">Belum ada data Tracer Study yang dapat dianalisis</h5>
            <p class="text-muted mb-0" style="font-size: 0.875rem;">Statistik akan tersedia setelah ada response yang di-submit.</p>
        </div>
    </div>
@else
    <!-- Question Statistics -->
    @forelse($questionStatistics as $index => $question)
        <div class="card mb-3">
            <!-- Question Header -->
            <div class="card-header py-3" style="background: #f9fafb;">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 1.5rem; height: 1.5rem; background: #e0e7ff; color: #4f46e5; font-size: 0.75rem; font-weight: 700;">
                                {{ $index + 1 }}
                            </span>
                            <span class="badge bg-secondary" style="font-size: 0.6875rem;">{{ $question['type_label'] }}</span>
                            @if(!$question['is_active'])
                                <span class="badge bg-gray-300" style="font-size: 0.6875rem; color: #6b7280;">Nonaktif</span>
                            @endif
                            @if($question['is_required'])
                                <span class="badge bg-danger" style="font-size: 0.6875rem;">Wajib</span>
                            @endif
                        </div>
                        <h6 class="mb-1" style="color: #111827; font-size: 0.9375rem;">{{ $question['question_text'] }}</h6>
                        @if($question['code'])
                            <p class="text-muted mb-0" style="font-size: 0.6875rem;">Kode: {{ $question['code'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Question Body -->
            <div class="card-body p-3">
                <!-- Response Summary -->
                <div class="d-flex align-items-center gap-4 mb-3 pb-3" style="border-bottom: 1px solid #f3f4f6;">
                    <div class="text-center">
                        <p class="mb-0" style="font-size: 1.25rem; font-weight: 700; color: #4f46e5;">{{ $question['total_answered'] }}</p>
                        <p class="text-muted mb-0" style="font-size: 0.6875rem;">Menjawab</p>
                    </div>
                    <div class="text-center">
                        <p class="mb-0" style="font-size: 1.25rem; font-weight: 700; color: #ca8a04;">{{ $question['total_unanswered'] }}</p>
                        <p class="text-muted mb-0" style="font-size: 0.6875rem;">Tidak</p>
                    </div>
                    <div class="text-center">
                        <p class="mb-0" style="font-size: 1.25rem; font-weight: 700; color: #6b7280;">{{ $question['total_submitted_responses'] }}</p>
                        <p class="text-muted mb-0" style="font-size: 0.6875rem;">Total</p>
                    </div>
                </div>

                <!-- Radio/Select Statistics -->
                @if(in_array($question['type'], ['radio', 'select']))
                    <div class="space-y-2">
                        @forelse($question['options'] as $option)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: #f9fafb;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle border-2 border-secondary d-flex align-items-center justify-content-center" style="width: 0.875rem; height: 0.875rem;"></div>
                                    <span style="font-size: 0.8125rem; color: #374151;">{{ $option['label'] }}</span>
                                    @if(!$option['is_active'])
                                        <span class="text-muted" style="font-size: 0.6875rem;">(Nonaktif)</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: #111827;">{{ $option['count'] }}</span>
                                    <span class="text-muted" style="font-size: 0.6875rem;">({{ $option['percentage'] }}%)</span>
                                    <div class="rounded-pill overflow-hidden" style="width: 5rem; height: 0.5rem; background: #e5e7eb;">
                                        <div class="h-100 rounded-pill {{ $option['is_active'] ? 'bg-primary' : 'bg-secondary' }}" style="width: {{ $option['percentage'] }}%; opacity: 0.7;"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">Tidak ada opsi tersedia</p>
                        @endforelse
                    </div>

                <!-- Checkbox Statistics -->
                @elseif($question['type'] === 'checkbox')
                    <div class="alert alert-info py-2 mb-3" style="font-size: 0.75rem; background: #dbeafe; border-color: #bfdbfe; color: #1e40af;">
                        <i class="fas fa-info-circle me-1"></i>
                        Responden dapat memilih lebih dari satu opsi. Total persentase dapat melebihi 100%.
                    </div>
                    <div class="space-y-2">
                        @forelse($question['options'] as $option)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: #f9fafb;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded border-2 border-secondary d-flex align-items-center justify-content-center" style="width: 0.875rem; height: 0.875rem;"></div>
                                    <span style="font-size: 0.8125rem; color: #374151;">{{ $option['label'] }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: #111827;">{{ $option['count'] }}</span>
                                    <span class="text-muted" style="font-size: 0.6875rem;">({{ $option['percentage'] }}%)</span>
                                    <div class="rounded-pill overflow-hidden" style="width: 5rem; height: 0.5rem; background: #e5e7eb;">
                                        <div class="h-100 rounded-pill {{ $option['is_active'] ? 'bg-success' : 'bg-secondary' }}" style="width: {{ $option['percentage'] }}%; opacity: 0.7;"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">Tidak ada opsi tersedia</p>
                        @endforelse
                    </div>

                <!-- Number Statistics -->
                @elseif($question['type'] === 'number')
                    <div class="row g-2">
                        <div class="col-3">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Min</p>
                                <p class="mb-0" style="font-size: 1.125rem; font-weight: 700; color: #4f46e5;">{{ $question['min'] ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Max</p>
                                <p class="mb-0" style="font-size: 1.125rem; font-weight: 700; color: #4f46e5;">{{ $question['max'] ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Rata-rata</p>
                                <p class="mb-0" style="font-size: 1.125rem; font-weight: 700; color: #4f46e5;">{{ $question['average'] ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Total</p>
                                <p class="mb-0" style="font-size: 1.125rem; font-weight: 700; color: #6b7280;">{{ $question['total_answered'] }}</p>
                            </div>
                        </div>
                    </div>

                <!-- Date Statistics -->
                @elseif($question['type'] === 'date')
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Paling Awal</p>
                                <p class="mb-0" style="font-size: 0.875rem; font-weight: 700; color: #4f46e5;">{{ $question['earliest'] ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Paling Akhir</p>
                                <p class="mb-0" style="font-size: 0.875rem; font-weight: 700; color: #4f46e5;">{{ $question['latest'] ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: #f9fafb;">
                                <p class="text-muted mb-1" style="font-size: 0.6875rem;">Total</p>
                                <p class="mb-0" style="font-size: 0.875rem; font-weight: 700; color: #6b7280;">{{ $question['total_answered'] }}</p>
                            </div>
                        </div>
                    </div>

                <!-- Text/Textarea -->
                @else
                    <p class="text-muted text-center py-3 mb-0" style="font-size: 0.8125rem;">Pertanyaan teks tidak dianalisis secara statistik.</p>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-question-circle text-4xl text-gray-300 mb-3"></i>
                <h5 class="mb-2" style="color: #374151;">Belum ada pertanyaan kuesioner</h5>
                <p class="text-muted mb-0" style="font-size: 0.875rem;">Silakan tambahkan pertanyaan terlebih dahulu.</p>
            </div>
        </div>
    @endforelse
@endif
@endsection
