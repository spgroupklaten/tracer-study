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
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Alumni</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalAlumni }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Laki-laki</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $totalLaki }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Perempuan</dt>
                <dd class="mt-1 text-3xl font-semibold text-pink-600">{{ $totalPerempuan }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Bekerja</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $totalBekerja }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Belum Bekerja</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $totalBelumBekerja }}</dd>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- By Program Studi -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6">
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
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500">Belum ada data alumni</p>
                @endif
            </div>
        </div>
        
        <!-- By Tahun Lulus -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6">
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
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500">Belum ada data alumni</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Latest Alumni -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Alumni Terbaru</h3>
            <a href="{{ route('alumni.index') }}" class="text-sm text-blue-600 hover:text-blue-900">Lihat semua →</a>
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
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumni->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alumni->nama_lengkap }}</td>
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data alumni</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
