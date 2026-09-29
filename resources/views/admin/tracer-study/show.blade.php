@extends('layouts.admin')

@section('title', 'Detail Tracer Study')

@section('page-title', 'Tracer Study')

@section('content')
<!-- Breadcrumb -->
<ol class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <li>
        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-home"></i>
        </a>
    </li>
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li>
        <a href="{{ route('admin.tracer-study.index') }}" class="hover:text-blue-600 transition-colors">Tracer Study</a>
    </li>
    <li><i class="fas fa-chevron-right text-xs"></i></li>
    <li class="text-gray-800 font-medium">{{ $profile->user?->name ?? '-' }}</li>
</ol>

<!-- Flash Messages -->
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
    <i class="fas fa-check-circle text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Alumni Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-user text-blue-600"></i>
            Data Alumni
        </h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                <p class="text-sm font-semibold text-gray-900">{{ $profile->user?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">NIM</p>
                <p class="text-sm font-semibold text-gray-900">{{ $profile->nim }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Program Studi</p>
                <p class="text-sm font-semibold text-gray-900">{{ $profile->program_studi }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tahun Lulus</p>
                <p class="text-sm font-semibold text-gray-900">{{ $profile->tahun_lulus ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tracer Study Status Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-clipboard-check text-purple-600"></i>
            Status Tracer Study
        </h3>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Status</p>
                @php
                    $statusLabel = $tracerStudy->isSubmitted() ? 'Submitted' : 'Draft';
                    $statusColors = [
                        'Draft' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                        'Submitted' => 'bg-green-100 text-green-800 border border-green-200',
                    ];
                @endphp
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$statusLabel] }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $tracerStudy->started_at ? $tracerStudy->started_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Submit</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $tracerStudy->submitted_at ? $tracerStudy->submitted_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Answers Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-list-check text-green-600"></i>
            Jawaban ({{ $questions->count() }} pertanyaan)
        </h3>
    </div>
    <div class="p-5">
        @forelse($questions as $question)
            <div class="mb-5 last:mb-0 pb-5 last:pb-0 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <dt class="text-sm font-medium text-gray-800 mb-2 flex items-start gap-2">
                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                        {{ $loop->iteration }}
                    </span>
                    <span>{{ $question->question_text }}
                        @if($question->is_required)
                            <span class="text-red-500">*</span>
                        @endif
                    </span>
                </dt>
                <dd class="text-sm text-gray-700 pl-8 bg-gray-50 rounded-lg p-3">
                    @php
                        $answer = $answers[$question->id] ?? null;
                        $displayAnswer = $answer;

                        if (is_array($answer)) {
                            $displayAnswer = implode(', ', $answer);
                        }
                    @endphp
                    {{ $displayAnswer ?: '-' }}
                </dd>
            </div>
        @empty
            <div class="text-center py-8">
                <i class="fas fa-clipboard-list text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Belum ada jawaban</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
