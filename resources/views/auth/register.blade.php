<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Absensi RFID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
  class="min-h-screen flex items-start justify-center p-4
         bg-center bg-cover bg-no-repeat bg-fixed relative"
  style="background-image: url('{{ asset('images/abdi.png') }}')">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Wrapper: form dinaikkan dengan padding-top -->
    <div class="relative w-full max-w-md space-y-6 pt-6 md:pt-10 lg:pt-16 pb-8 z-10" x-data="registerForm()">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-lg transform hover:scale-105 transition-transform duration-300">
                <i class="ri-scan-2-line ri-2x text-white"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Daftar Akun Baru</h2>
        </div>

        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-8">
            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ri-error-warning-line text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada input Anda:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-ref="registerForm">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ri-user-line text-gray-400"></i>
                        </div>
                        <input type="text" id="name" name="name" required
                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               placeholder="Masukkan nama lengkap"
                               value="{{ old('name') }}">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ri-mail-line text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               placeholder="Masukkan email"
                               value="{{ old('email') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ri-lock-line text-gray-400"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'"
                               id="password"
                               name="password"
                               required
                               class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               placeholder="Masukkan password (minimal 6 karakter)">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ri-lock-line text-gray-400"></i>
                        </div>
                        <input :type="showConfirmPassword ? 'text' : 'password'"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               placeholder="Masukkan ulang password">
                        <button type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i :class="showConfirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                        <i class="ri-user-add-line mr-2"></i>
                        <span>Daftar</span>
                    </button>
                </div>
                <div class="text-center pt-2">
                    <span class="text-sm text-gray-600">Sudah punya akun? </span>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Masuk disini
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function registerForm() {
        return {
            showPassword: false,
            showConfirmPassword: false
        }
    }
    </script>
</body>
</html>
