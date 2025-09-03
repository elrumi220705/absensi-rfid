@extends('layouts.app')

@section('content')
    @php
        // Pastikan nilai time menjadi HH:MM untuk input[type=time]
        $jm = old('jadwal_mulai', $materi->jadwal_mulai);
        $jp = old('jadwal_pulang', $materi->jadwal_pulang);
        $jmVal = $jm ? (preg_match('/^\d{2}:\d{2}:\d{2}$/', $jm) ? \Carbon\Carbon::createFromFormat('H:i:s', $jm)->format('H:i') : $jm) : '';
        $jpVal = $jp ? (preg_match('/^\d{2}:\d{2}:\d{2}$/', $jp) ? \Carbon\Carbon::createFromFormat('H:i:s', $jp)->format('H:i') : $jp) : '';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-4">
        <div class="max-w-5xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/60 p-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="ri-edit-2-line text-xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit Absen Harian</h1>
                        <p class="text-gray-600 text-sm">Ubah nama absen, jam mulai, dan jam pulang.</p>
                    </div>
                </div>
            </div>

            <!-- Form Edit Absen -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/60 p-6">
                <form action="{{ route('materi.update', $materi->id) }}" method="POST" class="space-y-6" id="formEdit">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Absen -->
                        <div class="md:col-span-2">
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Absen</label>
                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                value="{{ old('nama', $materi->nama) }}"
                                required
                                class="w-full md:w-72 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="cth: 150825 atau 15-08-25 - Upacara">
                            @error('nama') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @if (session('error')) <p class="mt-1 text-xs text-red-500">{{ session('error') }}</p> @endif
                        </div>

                        <!-- Catatan (opsional) -->
                        <div class="md:col-span-2">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
                            <input
                                type="text"
                                name="deskripsi"
                                id="deskripsi"
                                value="{{ old('deskripsi', $materi->deskripsi) }}"
                                placeholder="Misal: Ulangan harian / Agenda khusus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('deskripsi') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jam Mulai -->
                        <div>
                            <label for="jadwal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Jadwal Mulai</label>
                            <input
                                type="time"
                                name="jadwal_mulai"
                                id="jadwal_mulai"
                                value="{{ $jmVal }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('jadwal_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jam Pulang -->
                        <div>
                            <label for="jadwal_pulang" class="block text-sm font-medium text-gray-700 mb-2">Jadwal Pulang</label>
                            <input
                                type="time"
                                name="jadwal_pulang"
                                id="jadwal_pulang"
                                value="{{ $jpVal }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('jadwal_pulang') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('materi.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                            Simpan
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
            validateTime();

            // Ctrl/Cmd + S untuk submit cepat
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                    e.preventDefault();
                    document.getElementById('formEdit').requestSubmit();
                }
            });
        });
    </script>
@endsection
