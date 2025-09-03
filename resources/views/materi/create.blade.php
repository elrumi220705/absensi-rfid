@extends('layouts.app')

@section('content')
    @php
        // Default nama absen: ddmmyy (boleh diedit oleh user)
        $defaultNama = \Carbon\Carbon::now()->format('dmy');
    @endphp

    <div class="min-h-screen bg-gradient-to-br py-4">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="ri-add-line text-xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Buat Absen Harian</h1>
                        <p class="text-gray-600 text-sm">
                            Isi nama absen (misal: <span class="font-semibold">{{ $defaultNama }}</span>) dan jadwal waktunya.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Tambah Absen Harian -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6 mb-8">
                <form action="{{ route('materi.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Absen (editable) -->
                        <div class="md:col-span-2">
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Absen
                            </label>
                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                value="{{ old('nama', $defaultNama) }}"
                                required
                                class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="cth: {{ $defaultNama }}">
                            <p class="mt-1 text-xs text-gray-500">
                                Rekomendasi format: <b>ddmmyy</b> (misal: {{ $defaultNama }}). Boleh pakai format lain jika perlu.
                            </p>
                            @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @if(session('error'))
                                <p class="mt-1 text-xs text-red-500">{{ session('error') }}</p>
                            @endif
                        </div>

                        <!-- Catatan / Deskripsi (opsional) -->
                        <div class="md:col-span-2">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (opsional)
                            </label>
                            <input
                                type="text"
                                name="deskripsi"
                                id="deskripsi"
                                value="{{ old('deskripsi') }}"
                                placeholder="Misal: Ulangan harian, Upacara, dll."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('deskripsi') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jadwal Mulai -->
                        <div>
                            <label for="jadwal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal Mulai
                            </label>
                            <input
                                type="time"
                                name="jadwal_mulai"
                                id="jadwal_mulai"
                                value="{{ old('jadwal_mulai') }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('jadwal_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jadwal Pulang -->
                        <div>
                            <label for="jadwal_pulang" class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal Pulang
                            </label>
                            <input
                                type="time"
                                name="jadwal_pulang"
                                id="jadwal_pulang"
                                value="{{ old('jadwal_pulang') }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('jadwal_pulang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Tombol Action -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('materi.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                            <i class="ri-close-line w-4 h-4 inline mr-1"></i> Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm transition-all">
                            <i class="ri-save-line w-4 h-4 inline mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Validasi ringan agar pulang > mulai --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mulai  = document.getElementById('jadwal_mulai');
            const pulang = document.getElementById('jadwal_pulang');

            function validateTime() {
                if (mulai.value && pulang.value && pulang.value <= mulai.value) {
                    pulang.setCustomValidity('Jadwal pulang harus lebih akhir dari jadwal mulai.');
                } else {
                    pulang.setCustomValidity('');
                }
            }

            mulai?.addEventListener('change', validateTime);
            pulang?.addEventListener('change', validateTime);

            // init
            validateTime();
        });
    </script>
@endsection
