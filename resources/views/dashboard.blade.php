@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="min-h-screen bg-gradient-to-br py-4">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Header --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-6 mb-6">
                <div class="flex items-center justify-between gap-6">
                    {{-- Kiri: Judul --}}
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="ri-dashboard-line text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Dashboard Admin</h1>
                            <p class="text-gray-600">Kelola sistem absensi RFID dengan mudah dan efisien</p>
                        </div>
                    </div>

                    {{-- Kanan: Live Jam + Hari/Tanggal --}}
                    <div class="text-right">
                        <div id="liveClock"
                            class="font-extrabold text-3xl md:text-4xl text-blue-700 tracking-widest tabular-nums">
                            --:--:--
                        </div>
                        <div id="liveDay" class="text-sm text-gray-600 mt-1">
                            {{-- otomatis terisi oleh JS --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                        <i class="ri-user-line text-xl text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Murid</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalPeserta ?? 0 }}</h3>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mr-4">
                        <i class="ri-book-line text-xl text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total hari</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalMateri ?? 0 }}</h3>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                        <i class="ri-file-list-line text-xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Absensi</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalAbsensi ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            {{-- Quick Access + Chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Quick Access --}}
                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5 lg:col-span-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Akses Cepat</h3>
                    <div class="space-y-3">
                        <a href="{{ route('peserta.create') }}"
                            class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="w-8 h-8 rounded-md bg-blue-500 flex items-center justify-center mr-3">
                                <i class="ri-user-add-line text-white"></i>
                            </div>
                            <span class="text-sm font-medium">Tambah Murid Baru</span>
                        </a>
                        <a href="{{ route('materi.create') }}"
                            class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="w-8 h-8 rounded-md bg-purple-500 flex items-center justify-center mr-3">
                                <i class="ri-book-line text-white"></i>
                            </div>
                            <span class="text-sm font-medium">Tambah Hari</span>
                        </a>
                        <a href="{{ route('absensi.index') }}"
                            class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="w-8 h-8 rounded-md bg-green-500 flex items-center justify-center mr-3">
                                <i class="ri-qr-scan-line text-white"></i>
                            </div>
                            <span class="text-sm font-medium">Scan Absensi</span>
                        </a>
                        <a href="#"
                            class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                            <div class="w-8 h-8 rounded-md bg-orange-500 flex items-center justify-center mr-3">
                                <i class="ri-file-download-line text-white"></i>
                            </div>
                            <span class="text-sm font-medium">Export Semua Data</span>
                        </a>
                    </div>
                </div>

                {{-- Doughnut Chart --}}
                @php
                    // ====== Fallback builder agar "hari ini" terbaca kalau $hariData kosong ======
                    // Label default (Indonesia)
                    $__labelsDefault = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

                    // Pakai dari controller kalau ada, kalau tidak pakai default
                    $__labels = $hariLabels ?? $__labelsDefault;

                    // Data dari controller kalau ada
                    $__data = $hariData ?? null;

                    // Jika data belum ada ATAU semuanya 0 → bangun dari $recentAttendance (1 minggu terakhir)
                    $needsFallback = true;
                    if (is_array($__data) && count($__data) === count($__labels)) {
                        $sum = 0;
                        foreach ($__data as $v) {
                            $sum += (int) $v;
                        }
                        $needsFallback = $sum === 0;
                    }

                    if ($needsFallback) {
                        // Inisialisasi semua nol
                        $__data = array_fill(0, count($__labels), 0);

                        // Ambil event pada rentang minggu ini (Senin..Minggu)
                        $now = \Carbon\Carbon::now();
                        $monday = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                        $sunday = $now->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

                        foreach ($recentAttendance ?? [] as $att) {
                            $ts = \Carbon\Carbon::parse($att->created_at);
                            // filter hanya minggu ini
                            if ($ts->between($monday, $sunday)) {
                                $hariNama = $ts->locale('id')->translatedFormat('l'); // Senin..Minggu
                                $idx = array_search($hariNama, $__labels);
                                if ($idx === false) {
                                    // Jika label dari controller bukan full (mis. hanya Senin..Jumat),
                                    // mapping fallback ke 0..4 (skip Sabtu/Minggu)
                                    $idx = array_search($hariNama, $__labelsDefault);
                                    if ($idx !== false && $idx < count($__data)) {
                                        $__data[$idx] += 1;
                                    }
                                } else {
                                    $__data[$idx] += 1;
                                }
                            }
                        }

                        // Kalau label dari controller adalah 5 hari sekolah (Senin..Jumat), potong data juga
                        if (count($__labels) === 5) {
                            // Ambil indeks 0..4 (Senin..Jumat)
                            $__data = array_slice($__data, 0, 5);
                        }
                    }
                @endphp

                <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kehadiran per Hari (Minggu Ini)</h3>
                    <div class="h-72">
                        <canvas id="attendanceBarChart" data-labels='@json($hariLabels)'
                            data-hadir='@json($hariDataStatus['hadir'])' data-terlambat='@json($hariDataStatus['terlambat'])'
                            data-tidak='@json($hariDataStatus['tidak_hadir'])'></canvas>

                    </div>
                </div>
            </div>

            {{-- Daftar Kehadiran Hari Ini --}}
<div class="grid grid-cols-1">
    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Kehadiran Hari Ini</h3>

            {{-- Filter kelas --}}
            <form method="GET" action="{{ route('dashboard') }}">
                <select name="kelas" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas }}" {{ $filterKelas == $kelas ? 'selected' : '' }}>
                            {{ $kelas }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Datang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                    </tr>
                </thead>
                <tbody class="bg-white/60 divide-y divide-gray-200">
                    @forelse ($allPesertaWithAbsensi as $peserta)
                        @php
                            $absen = $peserta->absensi->first();
                            $status = $absen?->status ?? 'Belum Absen';
                        @endphp
                        <tr>
                            {{-- Foto --}}
                            <td class="px-4 py-3">
                                @if($peserta->foto)
                                    <img src="{{ asset('storage/'.$peserta->foto) }}"
                                         alt="Foto {{ $peserta->nama }}"
                                         class="w-10 h-10 rounded-full object-cover border">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="ri-user-line text-lg"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $peserta->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $peserta->kelas ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($status === 'hadir')
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs">Hadir</span>
                                @elseif ($status === 'terlambat')
                                    <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs">Terlambat</span>
                                @elseif ($status === 'tidak_hadir')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs">Tidak Hadir</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-xs">Belum Absen</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $absen?->waktu_datang ? \Carbon\Carbon::parse($absen->waktu_datang)->format('H:i') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $absen?->waktu_pulang ? \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-sm text-gray-500 text-center">
                                Tidak ada siswa untuk kelas ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


        </div>
    </div>

    {{-- Chart.js + datalabels --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    {{-- Live clock script --}}
    <script>
        (function() {
            const clockEl = document.getElementById('liveClock');
            const dayEl = document.getElementById('liveDay');
            if (!clockEl || !dayEl) return;

            function tick() {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
                dayEl.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('attendanceBarChart');
    if (!el) return;
    const ctx = el.getContext('2d');

    const labels     = JSON.parse(el.dataset.labels || '[]');
    const dataHadir  = JSON.parse(el.dataset.hadir || '[]').map(n => Number(n||0));
    const dataTelat  = JSON.parse(el.dataset.terlambat || '[]').map(n => Number(n||0));
    const dataTidak  = JSON.parse(el.dataset.tidak || '[]').map(n => Number(n||0));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Hadir',
                    data: dataHadir,
                    backgroundColor: '#22C55E'
                },
                {
                    label: 'Terlambat',
                    data: dataTelat,
                    backgroundColor: '#FACC15'
                },
                {
                    label: 'Tidak Hadir',
                    data: dataTidak,
                    backgroundColor: '#EF4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Kehadiran Siswa per Hari (Senin–Jumat)'
                }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
});

    </script>
@endsection
