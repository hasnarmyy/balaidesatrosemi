@extends('layouts.landing.main')

@section('title', 'Login - Manajemen Absensi dan Rekapitulasi Penggajian')

@section('content')

<body class="min-h-screen flex items-center justify-center p-4 relative">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/bg.jpg') }}" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 opacity-75"></div>
    </div>

    <div class="w-full max-w-5xl mx-auto relative z-10">
        <div class="backdrop-blur-sm bg-white/95 rounded-3xl shadow-2xl overflow-hidden">

            <div class="grid md:grid-cols-5 gap-0">

                <div
                    class="md:col-span-2 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 p-8 md:p-10 flex flex-col justify-center text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-5 rounded-full -ml-16 -mb-16"></div>

                    <div class="relative z-10">
                        <div class="mb-8">
                            <div
                                class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-sm">
                                <i class="fa fa-building text-3xl text-white"></i>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold mb-4 leading-tight">
                                Manajemen Absensi dan Rekapitulasi Penggajian
                            </h3>
                            <div class="w-16 h-1 bg-white/50 rounded-full mb-4"></div>
                            <p class="text-blue-100 text-base md:text-lg">
                                Balai Desa Trosemi
                            </p>
                        </div>

                        <div class="space-y-3 text-sm text-blue-100">
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-check-circle text-white"></i>
                                <span>Absensi Real-time</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-check-circle text-white"></i>
                                <span>Rekapitulasi Otomatis</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fa fa-check-circle text-white"></i>
                                <span>Laporan Penggajian</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3 p-8 md:p-12">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Selamat Datang</h2>
                            <p class="text-gray-500">Masuk ke akun Anda untuk melanjutkan</p>
                        </div>

                        @if (session('message'))
                        <div
                            class="mb-4 flex items-center gap-3 border border-red-200 bg-red-50 text-red-700 rounded-lg px-4 py-2 shadow-sm">
                            <i class="fa fa-exclamation-circle text-red-500 text-lg"></i>
                            <span class="text-sm font-medium">{{ session('message') }}</span>
                        </div>
                        @endif


                        <form method="POST" action="{{ route('auth.login') }}" class="space-y-6">
                            @csrf

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fa fa-envelope text-gray-400 group-focus-within:text-blue-900 transition-colors"></i>
                                    </div>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        oninput="this.value = this.value.toLowerCase()"
                                        pattern="^[a-z0-9._%+-]+@gmail\.com$"
                                        title="Email harus huruf kecil dan menggunakan domain @gmail.com"
                                        value="{{ old('email') }}"
                                        placeholder="nama@gmail.com"
                                        class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl
                                        focus:ring-2 focus:ring-blue-900 focus:border-blue-900
                                        transition duration-200 text-sm md:text-base bg-gray-50 focus:bg-white">
                                </div>
                                @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                                    <i class="fa fa-info-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>

                                <div class="relative group">
                                    <!-- Icon lock -->
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa fa-lock text-gray-400 group-focus-within:text-blue-900 transition-colors"></i>
                                    </div>

                                    <!-- Input password -->
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="••••••••"
                                        class="w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-900 focus:border-blue-900 transition duration-200 text-sm md:text-base bg-gray-50 focus:bg-white">

                                    <!-- Button eye -->
                                    <button
                                        type="button"
                                        onclick="togglePassword()"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-900 focus:outline-none">
                                        <i id="eyeIcon" class="fa fa-eye"></i>
                                    </button>
                                </div>

                                @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                                    <i class="fa fa-info-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                                @enderror
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-900 to-blue-800 hover:from-blue-800 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center space-x-2 text-base md:text-lg">
                                    <span>Masuk Sekarang</span>
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-500">
                                <i class="fa fa-shield text-primary"></i>

                                Data Anda terlindungi dengan aman
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection