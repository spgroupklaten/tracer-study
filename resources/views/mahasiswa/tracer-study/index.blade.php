@extends('layouts.app')

@section('title', 'Tracer Study')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tracer Study</h1>
        <p class="mt-1 text-sm text-gray-500">KuesionerTracer Study Alumni</p>
    </div>

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

    <!-- Status Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="p-6">
            @if($status === 'no_alumni')
                <!-- No Alumni Record -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Data Alumni Tidak Ditemukan</h3>
                    <p class="text-gray-500 mb-6">Silakan hubungi administrator untuk menambahkan data alumni Anda.</p>
                </div>
            @elseif($status === 'not_started')
                <!-- Not Started -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Mengisi Tracer Study</h3>
                    <p class="text-gray-500 mb-6">Silakan isi tracer study untuk membantu pengembangan kurikulum dan peningkatan mutu pendidikan.</p>
                    <a href="{{ route('mahasiswa.tracer-study.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Isi Tracer Study
                    </a>
                </div>
            @elseif($status === 'draft')
                <!-- Draft -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sedang Mengisi Tracer Study</h3>
                    <p class="text-gray-500 mb-6">Anda memiliki tracer study yang belum disubmit. Lanjutkan mengisi tracer study.</p>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('mahasiswa.tracer-study.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Lanjutkan Tracer Study
                        </a>
                    </div>
                </div>
            @elseif($status === 'submitted')
                <!-- Submitted -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tracer Study Sudah Disubmit</h3>
                    <p class="text-gray-500 mb-2">Tracer Study Anda telah berhasil disubmit pada:</p>
                    <p class="text-lg font-semibold text-gray-900 mb-4">
                        {{ $tracerStudy->submitted_at->format('d F Y, H:i') }}
                    </p>
                    <div class="p-4 bg-green-50 rounded-lg inline-block">
                        <p class="text-sm text-green-800">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            DataTracer Study terkunci dan tidak dapat diubah.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
        <h4 class="font-semibold text-blue-900 mb-2">Tentang Tracer Study</h4>
        <p class="text-sm text-blue-800">
            Tracer Study adalah survei untuk melacak jejak alumni setelah lulus dari pendidikan.
            Data ini digunakan untuk evaluasi kurikulum dan peningkatan mutu pembelajaran di institusi pendidikan.
        </p>
    </div>
</div>
@endsection
