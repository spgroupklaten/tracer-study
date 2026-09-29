@extends('layouts.admin')

@section('title', 'Tracer Study Responses')

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
    <li class="text-gray-800 font-medium">Tracer Study</li>
</ol>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Tracer Study Responses</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola respon tracer study alumni</p>
    </div>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
    <i class="fas fa-check-circle text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
    <i class="fas fa-exclamation-circle text-lg"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form action="{{ route('admin.tracer-study.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="md:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <div class="relative">
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ $search }}"
                       placeholder="Nama, NIM..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Program Studi Filter -->
        <div>
            <label for="program_studi" class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
            <select name="program_studi" id="program_studi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua</option>
                @foreach($programStudis as $ps)
                    <option value="{{ $ps }}" {{ $program_studi == $ps ? 'selected' : '' }}>{{ $ps }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tahun Lulus Filter -->
        <div>
            <label for="tahun_lulus" class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
            <select name="tahun_lulus" id="tahun_lulus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua</option>
                @foreach($tahunLulusOptions as $tahun)
                    <option value="{{ $tahun }}" {{ $tahun_lulus == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua</option>
                <option value="not_started" {{ $status === 'not_started' ? 'selected' : '' }}>Belum Mengisi</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted</option>
            </select>
        </div>

        <!-- Filter Buttons -->
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            <a href="{{ route('admin.tracer-study.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-300 transition-colors">
                <i class="fas fa-undo"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thn Lulus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($profiles as $key => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $profiles->firstItem() + $key }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->user?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->nim }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->program_studi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tahun_lulus }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusLabel = !$item->tracerStudy ? 'Belum' : ($item->tracerStudy->isSubmitted() ? 'Submitted' : 'Draft');
                                $statusColors = [
                                    'Belum' => 'bg-gray-100 text-gray-800',
                                    'Draft' => 'bg-yellow-100 text-yellow-800',
                                    'Submitted' => 'bg-green-100 text-green-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$statusLabel] }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tracerStudy?->submitted_at?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->tracerStudy)
                                <a href="{{ route('admin.tracer-study.show', $item) }}" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-clipboard-list text-5xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 mb-1">Belum ada data</p>
                                <p class="text-sm text-gray-500">Data tracer study akan muncul di sini setelah alumni mengisi.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($profiles->hasPages())
        <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Menampilkan {{ $profiles->firstItem() }} hingga {{ $profiles->lastItem() }} dari {{ $profiles->total() }} data
            </div>
            <div>
                {{ $profiles->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
