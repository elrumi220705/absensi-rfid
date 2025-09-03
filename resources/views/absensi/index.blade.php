@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br py-4">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="ri-file-list-line text-xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Absen Harian</h1>
                            <p class="text-gray-600 text-sm">Daftar absen harian yang sudah dibuat</p>
                        </div>
                    </div>
                    <a href="{{ route('materi.create') }}"
                       class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl px-5 py-3 font-semibold flex items-center justify-center gap-2 transition-all duration-300 shadow-md">
                        <i class="ri-add-line"></i>
                        <span>Buat Absen</span>
                    </a>
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($materis as $materi)
                    @php
                        // Normalisasi jam ke HH:MM saja untuk tampilan
                        $jm = $materi->jadwal_mulai;
                        $jp = $materi->jadwal_pulang;
                        if ($jm && preg_match('/^\d{2}:\d{2}:\d{2}$/', $jm)) {
                            $jm = \Carbon\Carbon::createFromFormat('H:i:s', $jm)->format('H:i');
                        }
                        if ($jp && preg_match('/^\d{2}:\d{2}:\d{2}$/', $jp)) {
                            $jp = \Carbon\Carbon::createFromFormat('H:i:s', $jp)->format('H:i');
                        }
                    @endphp

                    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 overflow-hidden hover:shadow-xl transition-all duration-300">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200/50">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $materi->nama }}</h3>
                        </div>

                        <!-- Body -->
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">{{ $materi->deskripsi ?: '—' }}</p>

                            <div class="flex flex-wrap items-center gap-2 text-xs mb-4">
                                <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                    Mulai: {{ $jm ?: '—' }}
                                </span>
                                <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Pulang: {{ $jp ?: '—' }}
                                </span>
                                <span class="px-2 py-1 rounded-full bg-gray-50 text-gray-700 border border-gray-100">
                                    Dibuat: {{ optional($materi->created_at)->format('d M Y') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <a href="{{ route('absensi.scan', $materi->id) }}"
                                   class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg py-2 px-3 flex items-center justify-center gap-2 transition-all duration-300 shadow-sm">
                                    <i class="ri-qr-scan-line"></i>
                                    <span>Scan Absensi</span>
                                </a>

                                <a href="{{ route('absensi.export', $materi->id) }}"
                                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg py-2 px-3 flex items-center justify-center gap-2 transition-all duration-300 shadow-sm">
                                    <i class="ri-download-line"></i>
                                    <span>Export</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(count($materis) === 0)
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-8 text-center mt-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-folder-info-line text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada absen harian</h3>
                    <p class="text-gray-600 mb-4">Silakan buat absen terlebih dahulu</p>
                    <a href="{{ route('materi.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all">
                        <i class="ri-add-line mr-2"></i> Buat Absen
                    </a>
                </div>
            @endif

            @if ($materis->hasPages())
                <div class="mt-6 bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4">
                    {{ $materis->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
