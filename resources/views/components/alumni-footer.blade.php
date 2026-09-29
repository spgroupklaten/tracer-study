<!-- Alumni & Career Center Footer -->
<footer class="bg-gradient-to-br from-blue-950 to-blue-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Column 1: About -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-blue-800 font-bold text-xl">UM</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Alumni & Career Center</h3>
                        <p class="text-blue-200 text-sm">Universitas Muhammadiyah Klaten</p>
                    </div>
                </div>
                <p class="text-blue-200 text-sm leading-relaxed mb-4">
                    Alumni & Career Center UMKlaten berfungsi sebagai jembatan antara alumni dan dunia kerja.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="p-2 bg-blue-800 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="p-2 bg-blue-800 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 class="text-lg font-bold mb-4">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-blue-200 hover:text-white transition-colors text-sm">Beranda</a></li>
                    <li><a href="{{ route('alumni.index') }}" class="text-blue-200 hover:text-white transition-colors text-sm">Profil Alumni</a></li>
                    <li><a href="#lowongan" class="text-blue-200 hover:text-white transition-colors text-sm">Lowongan Kerja</a></li>
                    <li><a href="#berita" class="text-blue-200 hover:text-white transition-colors text-sm">Berita</a></li>
                </ul>
            </div>

            <!-- Column 3: Account -->
            <div>
                <h4 class="text-lg font-bold mb-4">Akun</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="text-blue-200 hover:text-white transition-colors text-sm">Login Alumni</a></li>
                    <li><a href="{{ route('register') }}" class="text-blue-200 hover:text-white transition-colors text-sm">Registrasi</a></li>
                    <li><a href="{{ route('alumni.statistics') }}" class="text-blue-200 hover:text-white transition-colors text-sm">Statistik</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-blue-800 mt-10 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="text-blue-300 text-sm">
                        &copy; {{ date('Y') }} Alumni & Career Center - Universitas Muhammadiyah Klaten
                    </p>
                    <p class="text-blue-400 text-xs mt-1">
                        Jl. Yogyakarta - Solo Km.1 Klaten | Telp: (0272) 3321234
                    </p>
                </div>
                <div class="text-blue-300 text-xs">
                    Built with Laravel {{ app()->version() }}
                </div>
            </div>
        </div>
    </div>
</footer>
