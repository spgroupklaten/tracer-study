<x-guest-layout>
    <div class="min-h-screen bg-campus flex items-center justify-center p-4">
        <!-- Main Panel -->
        <div class="w-[95vw] md:w-[85vw] lg:w-[60vw] h-[500px] lg:h-[450px] max-w-[950px] bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-white/20 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 px-5 py-3 text-center flex-shrink-0">
                <h1 class="text-lg sm:text-xl font-bold text-white tracking-wide">ALUMNI & CAREER</h1>
                <p class="text-blue-200 text-xs">Universitas Muhammadiyah Klaten</p>
            </div>

            <!-- Content - 2 Column Layout -->
            <div class="flex-1 flex min-h-0">
                <!-- Left Panel - Branding -->
                <div class="hidden lg:flex lg:w-[42%] flex-col items-center justify-center bg-gradient-to-br from-blue-900 to-blue-800 p-4 text-white">
                    <!-- Logo -->
                    <div class="w-20 h-20 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center mb-3 border border-white/20">
                        <span class="text-white font-bold text-2xl">UM</span>
                    </div>

                    <!-- Branding Text -->
                    <h2 class="text-lg font-bold text-center mb-1">ALUMNI & CAREER</h2>
                    <p class="text-blue-200 text-xs text-center mb-3">Universitas Muhammadiyah Klaten</p>

                    <!-- Divider -->
                    <div class="w-8 h-0.5 bg-yellow-400 mb-3"></div>

                    <!-- Tagline -->
                    <p class="text-sm font-medium text-white/90">Connect, Grow & Succeed</p>
                </div>

                <!-- Vertical Divider -->
                <div class="hidden lg:block w-px bg-blue-200/30"></div>

                <!-- Right Panel - Login Form -->
                <div class="flex-1 lg:w-[58%] p-5 sm:p-6 flex flex-col justify-center">
                    <!-- Mobile Logo (only visible on mobile/tablet) -->
                    <div class="lg:hidden flex flex-col items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-lg">UM</span>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">ALUMNI & CAREER</h2>
                    </div>

                    <!-- Form Header -->
                    <div class="mb-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Login</h2>
                        <p class="text-gray-500 text-sm">Masuk ke Alumni & Career</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-3">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com"
                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-400 bg-red-50 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan kata sandi"
                                    class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-900 placeholder-gray-400 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-400 bg-red-50 @enderror"
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors p-1"
                                >
                                    <svg id="eye-icon-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <span class="ml-1.5 text-xs text-gray-600">Ingat saya</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                        >
                            LOGIN
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-3 text-center">
                        <p class="text-xs text-gray-500">
                            Belum memiliki akun?
                            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                                Daftar sebagai Alumni
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('eye-icon-' + inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</x-guest-layout>
