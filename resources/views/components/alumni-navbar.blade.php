<!-- Alumni & Career Center Navigation -->
<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-gradient-to-r from-blue-900 to-blue-800 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 lg:h-20">
            <!-- Logo & Branding -->
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <!-- University Logo Placeholder -->
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-white rounded-full flex items-center justify-center shadow-md">
                            <span class="text-blue-800 font-bold text-lg lg:text-xl">UM</span>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-white font-bold text-lg lg:text-xl tracking-wide">Alumni & Career</span>
                            <span class="hidden lg:block text-blue-200 text-xs">Universitas Muhammadiyah Klaten</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ route('home') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    Beranda
                </a>
                <a href="#lowongan"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-blue-100 hover:bg-white/10 hover:text-white">
                    Lowongan Kerja
                </a>
                <a href="#berita"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-blue-100 hover:bg-white/10 hover:text-white">
                    Berita
                </a>
            </div>

            <!-- Right Side Actions -->
            <div class="hidden lg:flex items-center gap-3">
                <!-- Search Icon -->
                <button class="p-2 rounded-lg text-blue-100 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                <!-- Login Button -->
                @guest
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-5 py-2 bg-yellow-400 text-blue-900 font-bold rounded-lg shadow-md hover:bg-yellow-300 transition-all duration-200 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        LOGIN
                    </a>
                @endguest

                @auth
                    <div class="flex items-center gap-3">
                        <span class="text-blue-100 text-sm">{{ Auth::user()->name }}</span>
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('mahasiswa.dashboard') }}"
                           class="px-4 py-2 bg-white/20 text-white font-medium rounded-lg hover:bg-white/30 transition-colors text-sm">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-blue-100 hover:text-white transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex lg:hidden items-center gap-2">
                @guest
                    <a href="{{ route('login') }}"
                       class="px-3 py-1.5 bg-yellow-400 text-blue-900 font-bold rounded-lg text-xs">
                        Login
                    </a>
                @endguest
                @auth
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('mahasiswa.dashboard') }}"
                       class="px-3 py-1.5 bg-white/20 text-white font-medium rounded-lg text-xs">
                        Dashboard
                    </a>
                @endauth
                <button @click="open = ! open" class="p-2 rounded-lg text-blue-100 hover:bg-white/10 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-blue-700">
        <div class="px-4 py-3 space-y-1 bg-blue-900">
            <a href="{{ route('home') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                Beranda
            </a>
            <a href="#lowongan"
               class="block px-4 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-white/10 hover:text-white">
                Lowongan Kerja
            </a>
            <a href="#berita"
               class="block px-4 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-white/10 hover:text-white">
                Berita
            </a>
            @auth
                <div class="pt-3 border-t border-blue-700 mt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-medium text-blue-100 hover:bg-white/10 hover:text-white rounded-lg">
                            Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
