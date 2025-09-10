<nav class="bg-white border-b border-gray-200 fixed top-16 left-0 right-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-8">
            <a href="{{ route('dashboard') }}"
                class="border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium">
                Dashboard
            </a>
            <a href="{{ route('peserta.index') }}"
                class="border-b-2 {{ request()->routeIs('peserta.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium">
                Murid
            </a>
            <a href="{{ route('jadwal.index') }}"
                class="border-b-2 {{ request()->routeIs('jadwal.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium">
                Jadwal
            </a>
            <a href="{{ route('materi.index') }}"
                class="border-b-2 {{ request()->routeIs('materi.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium">
                Hari
            </a>
            <a href="{{ route('absensi.index') }}"
                class="border-b-2 {{ request()->routeIs('absensi.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium">
                Absensi
            </a>
        </div>
    </div>
</nav>
