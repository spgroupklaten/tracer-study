@extends('layouts.app')

@section('title', 'Isi Tracer Study')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Isi Tracer Study</h1>
                <p class="mt-1 text-sm text-gray-500">Lengkapi semua pertanyaan di bawah ini</p>
            </div>
            <a href="{{ route('mahasiswa.tracer-study.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Check for empty questions -->
    @if($questions->isEmpty())
        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Kuesioner belum tersedia</h3>
            <p class="mt-1 text-sm text-gray-500">Saat ini belum ada pertanyaan aktif. Silakan hubungi administrator.</p>
        </div>
    @else
        <!-- Form -->
        <form action="{{ route('mahasiswa.tracer-study.save') }}" method="POST" id="tracer-form">
            @csrf
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 space-y-8">
                    @php
                        $questionNumber = 0;
                    @endphp

                    @foreach($questions as $question)
                        @php
                            $questionNumber++;
                            $questionId = $question->id;
                            $answer = $answers[$questionId] ?? null;
                            $errorKey = 'answers.' . $questionId;
                            $hasError = $errors->has($errorKey);
                            $errorMessage = $errors->first($errorKey);

                            // Get options from relational table
                            $options = $question->activeQuestionOptions;
                            $isChoiceType = $question->isChoiceType();
                            $hasOptions = $isChoiceType && $options->isNotEmpty();

                            // Get type for comparison
                            $typeValue = $question->type->value;
                        @endphp

                        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-900 mb-2">
                                    {{ $questionNumber }}. {{ $question->question_text }}
                                    @if($question->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                {{-- TEXT Input --}}
                                @if($typeValue === 'text')
                                    <input type="text"
                                           name="answers[{{ $questionId }}]"
                                           id="answers_{{ $questionId }}"
                                           value="{{ old('answers.' . $questionId, is_string($answer) ? $answer : '') }}"
                                           class="w-full rounded-lg border @error($errorKey) border-red-500 @else border-gray-300 @enderror shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                                           placeholder="Masukkan jawaban Anda">
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- TEXTAREA --}}
                                @elseif($typeValue === 'textarea')
                                    <textarea name="answers[{{ $questionId }}]"
                                              id="answers_{{ $questionId }}"
                                              rows="4"
                                              class="w-full rounded-lg border @error($errorKey) border-red-500 @else border-gray-300 @enderror shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                                              placeholder="Masukkan jawaban Anda">{{ old('answers.' . $questionId, is_string($answer) ? $answer : '') }}</textarea>
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- NUMBER --}}
                                @elseif($typeValue === 'number')
                                    <input type="number"
                                           name="answers[{{ $questionId }}]"
                                           id="answers_{{ $questionId }}"
                                           value="{{ old('answers.' . $questionId, is_numeric($answer) ? $answer : '') }}"
                                           class="w-full rounded-lg border @error($errorKey) border-red-500 @else border-gray-300 @enderror shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2"
                                           placeholder="Masukkan angka">
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- DATE --}}
                                @elseif($typeValue === 'date')
                                    <input type="date"
                                           name="answers[{{ $questionId }}]"
                                           id="answers_{{ $questionId }}"
                                           value="{{ old('answers.' . $questionId, is_string($answer) ? $answer : '') }}"
                                           class="w-full rounded-lg border @error($errorKey) border-red-500 @else border-gray-300 @enderror shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- RADIO --}}
                                @elseif($typeValue === 'radio')
                                    @if($hasOptions)
                                        <div class="space-y-2">
                                            @foreach($options as $optionIndex => $option)
                                                <label class="flex items-center p-3 rounded-lg border @error($errorKey) border-red-500 @else border-gray-200 hover:border-gray-300 @enderror cursor-pointer transition-colors">
                                                    <input type="radio"
                                                           name="answers[{{ $questionId }}]"
                                                           value="{{ $option->value }}"
                                                           {{ old('answers.' . $questionId, is_string($answer) ? $answer : '') == $option->value ? 'checked' : '' }}
                                                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                    <span class="ml-3 text-sm text-gray-700">{{ $option->label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-yellow-600 italic">Opsi belum tersedia untuk pertanyaan ini.</p>
                                    @endif
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- SELECT --}}
                                @elseif($typeValue === 'select')
                                    @if($hasOptions)
                                        <select name="answers[{{ $questionId }}]"
                                                id="answers_{{ $questionId }}"
                                                class="w-full rounded-lg border @error($errorKey) border-red-500 @else border-gray-300 @enderror shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                                            <option value="">-- Pilih --</option>
                                            @foreach($options as $option)
                                                <option value="{{ $option->value }}" {{ old('answers.' . $questionId, is_string($answer) ? $answer : '') == $option->value ? 'selected' : '' }}>
                                                    {{ $option->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p class="text-sm text-yellow-600 italic">Opsi belum tersedia untuk pertanyaan ini.</p>
                                    @endif
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                {{-- CHECKBOX --}}
                                @elseif($typeValue === 'checkbox')
                                    @if($hasOptions)
                                        <div class="space-y-2">
                                            @foreach($options as $option)
                                                @php
                                                    $isChecked = is_array($answer) && in_array($option->value, $answer);
                                                @endphp
                                                <label class="flex items-center p-3 rounded-lg border @error($errorKey) border-red-500 @else border-gray-200 hover:border-gray-300 @enderror cursor-pointer transition-colors">
                                                    <input type="checkbox"
                                                           name="answers[{{ $questionId }}][]"
                                                           value="{{ $option->value }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="ml-3 text-sm text-gray-700">{{ $option->label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-yellow-600 italic">Opsi belum tersedia untuk pertanyaan ini.</p>
                                    @endif
                                    @error($errorKey)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Wajib diisi
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                                formaction="{{ route('mahasiswa.tracer-study.save') }}"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors font-medium">
                            Simpan Draft
                        </button>
                        <button type="submit"
                                formaction="{{ route('mahasiswa.tracer-study.submit') }}"
                                form="tracer-form"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Submit Tracer Study
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
