@extends('layouts.app')

@section('title', 'Profil Alumni')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('alumni.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Profil Alumni</h1>
            </div>
        </div>
    </div>

    <!-- Detail Card - PUBLIC VIEW (no sensitive data) -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-2xl font-bold text-blue-600">
                            {{ strtoupper(substr($profile->nama_lengkap, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $profile->nama_lengkap }}</h2>
                    <p class="text-sm text-gray-500">{{ $profile->nim }} - {{ $profile->program_studi }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Content - PUBLIC SAFE DATA ONLY -->
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <!-- NIM -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">NIM</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->nim }}</dd>
                </div>

                <!-- Jenis Kelamin -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->jenis_kelamin_display }}</dd>
                </div>

                <!-- Tahun Masuk -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tahun Masuk</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->tahun_masuk }}</dd>
                </div>

                <!-- Tahun Lulus -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tahun Lulus</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->tahun_lulus ?? '-' }}</dd>
                </div>

                <!-- Program Studi -->
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Program Studi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->program_studi }}</dd>
                </div>

                <!-- IPK -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">IPK</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->ipk ? number_format($profile->ipk, 2) : '-' }}</dd>
                </div>

                <!-- Status Akademik -->
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $profile->academic_status_label }}</dd>
                </div>
            </dl>

            <!-- Privacy Notice -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Data kontak (email, nomor HP, alamat) dilindungi privasi dan tidak ditampilkan untuk umum.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
