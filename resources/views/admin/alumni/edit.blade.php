@extends('layouts.app')

@section('title', 'Edit Alumni')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.alumni.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Alumni</h1>
        </div>
    </div>
    
    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.alumni.update', ['alumni' => $alumni->id]) }}" method="POST" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')
            
            <!-- Data Pribadi -->
            <div class="px-6 py-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Data Pribadi</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- NIM -->
                    <div>
                        <label for="nim" class="block text-sm font-medium text-gray-700 mb-1">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nim" 
                               id="nim" 
                               value="{{ old('nim', $alumni->nim) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('nim') ? 'border-red-500' : '' }}"
                               required>
                        @error('nim')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               id="nama_lengkap" 
                               value="{{ old('nama_lengkap', $alumni->nama_lengkap) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('nama_lengkap') ? 'border-red-500' : '' }}"
                               required>
                        @error('nama_lengkap')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $alumni->jenis_kelamin) == 'L' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" required>
                                <span class="ml-2 text-sm text-gray-700">Laki-laki</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $alumni->jenis_kelamin) == 'P' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" required>
                                <span class="ml-2 text-sm text-gray-700">Perempuan</span>
                            </label>
                        </div>
                        @error('jenis_kelamin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tempat & Tanggal Lahir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="tempat_lahir" 
                                   id="tempat_lahir" 
                                   value="{{ old('tempat_lahir', $alumni->tempat_lahir) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('tempat_lahir') ? 'border-red-500' : '' }}"
                                   required>
                            @error('tempat_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_lahir" 
                                   id="tanggal_lahir" 
                                   value="{{ old('tanggal_lahir', $alumni->tanggal_lahir->format('Y-m-d')) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('tanggal_lahir') ? 'border-red-500' : '' }}"
                                   required>
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kontak -->
            <div class="px-6 py-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontak</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $alumni->email) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : '' }}"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- nomor HP -->
                    <div>
                        <label for="nomor_hp" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor HP
                        </label>
                        <input type="text" 
                               name="nomor_hp" 
                               id="nomor_hp" 
                               value="{{ old('nomor_hp', $alumni->nomor_hp) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('nomor_hp') ? 'border-red-500' : '' }}">
                        @error('nomor_hp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <textarea name="alamat" 
                                  id="alamat" 
                                  rows="3" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('alamat') ? 'border-red-500' : '' }}">{{ old('alamat', $alumni->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Data Akademik -->
            <div class="px-6 py-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Data Akademik</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Tahun Masuk & Lulus -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tahun_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                                Tahun Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="tahun_masuk" 
                                   id="tahun_masuk" 
                                   value="{{ old('tahun_masuk', $alumni->tahun_masuk) }}"
                                   min="1990" 
                                   max="{{ date('Y') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('tahun_masuk') ? 'border-red-500' : '' }}"
                                   required>
                            @error('tahun_masuk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tahun_lulus" class="block text-sm font-medium text-gray-700 mb-1">
                                Tahun Lulus <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="tahun_lulus" 
                                   id="tahun_lulus" 
                                   value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}"
                                   min="1990" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('tahun_lulus') ? 'border-red-500' : '' }}"
                                   required>
                            @error('tahun_lulus')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Program Studi -->
                    <div>
                        <label for="program_studi" class="block text-sm font-medium text-gray-700 mb-1">
                            Program Studi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="program_studi" 
                               id="program_studi" 
                               value="{{ old('program_studi', $alumni->program_studi) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('program_studi') ? 'border-red-500' : '' }}"
                               required>
                        @error('program_studi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Pekerjaan -->
            <div class="px-6 py-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pekerjaan</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Status Pekerjaan -->
                    <div>
                        <label for="status_pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">
                            Status Pekerjaan
                        </label>
                        <select name="status_pekerjaan" 
                                id="status_pekerjaan" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('status_pekerjaan') ? 'border-red-500' : '' }}">
                            <option value="">Pilih Status</option>
                            <option value="Belum Bekerja" {{ old('status_pekerjaan', $alumni->status_pekerjaan) == 'Belum Bekerja' ? 'selected' : '' }}>Belum Bekerja</option>
                            <option value="Bekerja" {{ old('status_pekerjaan', $alumni->status_pekerjaan) == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                            <option value="Wiraswasta" {{ old('status_pekerjaan', $alumni->status_pekerjaan) == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                            <option value="Studi Lanjut" {{ old('status_pekerjaan', $alumni->status_pekerjaan) == 'Studi Lanjut' ? 'selected' : '' }}>Studi Lanjut</option>
                            <option value="Lainnya" {{ old('status_pekerjaan', $alumni->status_pekerjaan) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('status_pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">
                            Pekerjaan / Jabatan
                        </label>
                        <input type="text" 
                               name="pekerjaan" 
                               id="pekerjaan" 
                               value="{{ old('pekerjaan', $alumni->pekerjaan) }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('pekerjaan') ? 'border-red-500' : '' }}">
                        @error('pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                            Keterangan
                        </label>
                        <textarea name="keterangan" 
                                  id="keterangan" 
                                  rows="3" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border {{ $errors->has('keterangan') ? 'border-red-500' : '' }}">{{ old('keterangan', $alumni->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <a href="{{ route('admin.alumni.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
