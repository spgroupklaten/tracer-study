@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="px-8 py-10 md:px-12">
            <div class="max-w-3xl">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-green-100 mb-4">
                    Role: <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                        {{ Auth::user()->role->label() }}
                    </span>
                </p>
                <p class="text-green-100">
                    Anda berhasil login sebagai Mahasiswa. Silakan isi kuesioner tracer study untuk membantu pengembangan kurikulum.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Tracer Study Info -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tracer Study</h3>
                        <p class="text-sm text-gray-500">Kuesioner alumni</p>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">
                    Tracer study adalah survei untuk melacak jejak alumni setelah lulus. 
                    Data ini digunakan untuk evaluasi dan pengembangan kurikulum.
                </p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Coming Soon:</strong> Modul Tracer Study sedang dalam pengembangan.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Profile Info -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Profil Saya</h3>
                        <p class="text-sm text-gray-500">Kelola informasi akun</p>
                    </div>
                </div>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="text-sm font-medium text-gray-500 w-24">Nama:</dt>
                        <dd class="text-sm text-gray-900">{{ Auth::user()->name }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="text-sm font-medium text-gray-500 w-24">Email:</dt>
                        <dd class="text-sm text-gray-900">{{ Auth::user()->email }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="text-sm font-medium text-gray-500 w-24">Role:</dt>
                        <dd class="text-sm text-gray-900">{{ Auth::user()->role->label() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
    
    <!-- Placeholder for Future Features -->
    <div class="bg-gray-100 rounded-xl p-8 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Fitur Sedang Dikembangkan</h3>
        <p class="text-gray-600">Modul Tracer Study, Riwayat, dan fitur lainnya akan segera hadir.</p>
    </div>
</div>
@endsection
