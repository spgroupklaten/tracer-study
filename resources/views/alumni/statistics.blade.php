@extends('layouts.app')

@section('title', 'Statistik Alumni')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Statistik Alumni</h1>
        <p class="mt-1 text-sm text-gray-500">Statistik dan visualisasi data alumni</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Alumni</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalAlumni }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Laki-laki</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $totalLaki }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Perempuan</dt>
                <dd class="mt-1 text-3xl font-semibold text-pink-600">{{ $totalPerempuan }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Bekerja</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $totalBekerja }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Belum Bekerja</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $totalBelumBekerja }}</dd>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- By Program Studi -->
        <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-medium text-gray-900">Alumni berdasarkan Program Studi</h3>
            </div>
            <div class="px-4 py-4 sm:p-6">
                @if($byProgramStudi->count() > 0)
                    <div class="space-y-4">
                        @foreach($byProgramStudi as $item)
                            @php
                                $percentage = $totalAlumni > 0 ? round(($item->total / $totalAlumni) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $item->program_studi }}</span>
                                    <span class="text-sm text-gray-500">{{ $item->total }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada data alumni</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- By Tahun Lulus -->
        <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
            <div class="px-4 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-medium text-gray-900">Alumni berdasarkan Tahun Lulus</h3>
            </div>
            <div class="px-4 py-4 sm:p-6">
                @if($byTahunLulus->count() > 0)
                    <div class="space-y-4">
                        @foreach($byTahunLulus as $item)
                            @php
                                $maxTotal = $byTahunLulus->max('total');
                                $percentage = $maxTotal > 0 ? round(($item->total / $maxTotal) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Tahun {{ $item->tahun_lulus }}</span>
                                    <span class="text-sm text-gray-500">{{ $item->total }} alumni</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada data alumni</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Latest Alumni -->
    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
        <div class="px-4 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Alumni Terbaru</h3>
            <a href="{{ route('alumni.index') }}" class="text-sm text-blue-600 hover:text-blue-900 flex items-center gap-1">
                Lihat semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Lulus</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($latestAlumni as $alumni)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumni->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($alumni->nama_lengkap, 0, 1)) }}</span>
                                    </div>
                                    {{ $alumni->nama_lengkap }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $alumni->program_studi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $alumni->tahun_lulus }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Bekerja' => 'bg-green-100 text-green-800',
                                        'Belum Bekerja' => 'bg-yellow-100 text-yellow-800',
                                        'Wiraswasta' => 'bg-blue-100 text-blue-800',
                                        'Studi Lanjut' => 'bg-purple-100 text-purple-800',
                                        'Lainnya' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $colorClass = $statusColors[$alumni->status_pekerjaan] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                    {{ $alumni->status_pekerjaan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('alumni.show', $alumni) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500">Belum ada data alumni</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
