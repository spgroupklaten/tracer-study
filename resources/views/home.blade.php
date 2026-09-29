@extends('layouts.app')

@section('title', 'Beranda - Alumni & Career Center UMKlaten')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-72 h-72 bg-yellow-400 rounded-full filter blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400 rounded-full filter blur-3xl transform translate-x-1/3 translate-y-1/3"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Hero Content -->
            <div class="text-center lg:text-left">
                <span class="inline-block px-4 py-1.5 bg-yellow-400 text-blue-900 text-sm font-bold rounded-full mb-6">
                    ALUMNI & CAREER CENTER
                </span>
                <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white mb-6 leading-tight">
                    REACH NEW HEIGHTS:
                    <span class="text-yellow-400">CONNECT, GROW & SUCCEED</span>
                </h1>
                <p class="text-lg lg:text-xl text-blue-100 mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Welcome to Alumni & Career Center. Your Gateway to Professional Excellence and Lifelong Network.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#lowongan"
                       class="inline-flex items-center justify-center px-8 py-4 bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg hover:bg-yellow-300 transition-all duration-200 text-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        EXPLORE CAREERS
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white font-bold rounded-xl shadow-lg hover:bg-white/20 transition-all duration-200 border border-white/30 text-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        REGISTER ALUMNI
                    </a>
                </div>
            </div>

            <!-- Hero Stats -->
            <div class="hidden lg:block">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <p class="text-4xl font-extrabold text-yellow-400 mb-2">{{ $totalAlumni }}</p>
                        <p class="text-blue-100 text-sm font-medium">Total Alumni</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <p class="text-4xl font-extrabold text-yellow-400 mb-2">{{ $alumniLaki + $alumniPerempuan > 0 ? round(($alumniLaki / ($alumniLaki + $alumniPerempuan)) * 100) : 0 }}%</p>
                        <p class="text-blue-100 text-sm font-medium">Bekerja</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <p class="text-4xl font-extrabold text-yellow-400 mb-2">100+</p>
                        <p class="text-blue-100 text-sm font-medium">Mitra Perusahaan</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <p class="text-4xl font-extrabold text-yellow-400 mb-2">50+</p>
                        <p class="text-blue-100 text-sm font-medium">Lowongan Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Bottom -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F9FAFB"/>
        </svg>
    </div>
</section>

<!-- Featured Career Opportunities Section -->
<section id="lowongan" class="py-16 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full mb-4">
                CAREER OPPORTUNITIES
            </span>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Featured Career Opportunities
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Temukan peluang karier terbaik dari perusahaan-perusahaan ternama yang bermitra dengan kami.
            </p>
        </div>

        <!-- Career Cards - Dummy Data for Preview -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Job Card 1 -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">IT</span>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Open</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Software Engineer</h3>
                    <p class="text-gray-500 text-sm mb-4">PT Teknologi Indonesia Maju</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Full-time</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Klaten</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">IT</span>
                    </div>
                    <a href="#" class="block w-full text-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Job Card 2 -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <span class="text-yellow-600 font-bold text-lg">MK</span>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Open</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Marketing Manager</h3>
                    <p class="text-gray-500 text-sm mb-4">PT Marketing Kreatif Indonesia</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Full-time</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Jakarta</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Marketing</span>
                    </div>
                    <a href="#" class="block w-full text-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>

            <!-- Job Card 3 -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-lg">FD</span>
                        </div>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Urgent</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Frontend Developer</h3>
                    <p class="text-gray-500 text-sm mb-4">Digital Innovation Corp</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Remote</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">IT</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Frontend</span>
                    </div>
                    <a href="#" class="block w-full text-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <!-- View All Link -->
        <div class="text-center mt-10">
            <a href="#" class="inline-flex items-center px-6 py-3 text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                Lihat Semua Lowongan
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Alumni Success Stories Section -->
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1.5 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full mb-4">
                SUCCESS STORIES
            </span>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Alumni Success Stories
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Inspirasi dari alumni-alumni terbaik yang telah berhasil membangun karier cemerlang.
            </p>
        </div>

        <!-- Success Stories Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Story Card 1 -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        AS
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Andi Saputra</h4>
                        <p class="text-sm text-gray-500">Informatika 2019</p>
                        <p class="text-sm text-blue-600">Software Engineer at Gojek</p>
                    </div>
                </div>
                <div class="text-4xl text-blue-200 leading-none mb-3">"</div>
                <p class="text-gray-600 italic">
                    Melalui Alumni & Career Center, saya berhasil mendapatkan informasi lowongan yang sesuai dengan skill saya. Sekarang saya bisa berkontribusi di perusahaan teknologi terbesar di Indonesia.
                </p>
            </div>

            <!-- Story Card 2 -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        RW
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Rina Wijayanti</h4>
                        <p class="text-sm text-gray-500">Sistem Informasi 2020</p>
                        <p class="text-sm text-purple-600">Product Manager at Tokopedia</p>
                    </div>
                </div>
                <div class="text-4xl text-purple-200 leading-none mb-3">"</div>
                <p class="text-gray-600 italic">
                    Jejaring alumni sangat membantu saya dalam membangun karier. Saya mendapat mentorship dari alumni senior yang kini menduduki posisi strategis di perusahaan ternama.
                </p>
            </div>

            <!-- Story Card 3 -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        BP
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Budi Prasetyo</h4>
                        <p class="text-sm text-gray-500">Manajemen 2018</p>
                        <p class="text-sm text-green-600">Business Owner</p>
                    </div>
                </div>
                <div class="text-4xl text-green-200 leading-none mb-3">"</div>
                <p class="text-gray-600 italic">
                    Berkat data tracer study, saya bisa melacak perkembangan alumni lain dan membangun networking yang kuat. Kini saya berhasil membangun bisnis saya sendiri.
                </p>
            </div>
    </div>
</section>

<!-- Latest News & Events Section -->
<section id="berita" class="py-16 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full mb-4">
                NEWS & EVENTS
            </span>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                Latest News & Events
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Tetap terhubung dengan informasi terbaru tentang kegiatan dan acara alumni.
            </p>
        </div>

        <!-- News Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($berita as $item)
                <article class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                    @if(isset($item['image']) && $item['image'])
                        <div class="h-48">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-blue-600 font-medium">{{ $item['date'] }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $item['excerpt'] }}</p>
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                            Baca Selengkapnya
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <p class="text-gray-500">Belum ada berita terbaru</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA Section -->
@guest
<section class="py-16 bg-gradient-to-r from-yellow-400 to-yellow-500">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl lg:text-3xl font-bold text-blue-900 mb-4">
            Siap Menjadi Bagian dari Alumni & Career Center?
        </h2>
        <p class="text-blue-800 mb-8 max-w-2xl mx-auto">
            Daftarkan diri Anda sebagai alumni dan nikmati berbagai manfaat dari jaringan alumni kami.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-blue-900 text-white font-bold rounded-xl shadow-lg hover:bg-blue-800 transition-colors text-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Register Sekarang
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-900 font-bold rounded-xl shadow-lg hover:bg-blue-50 transition-colors text-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Login Alumni
            </a>
        </div>
    </div>
</section>
@endguest
@endsection
