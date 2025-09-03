@extends('layouts.app')

{{-- Matikan toast/global alerts khusus halaman ini --}}
@section('hide_alerts', true)

@php
    /** @var \App\Models\Materi $materi */
    /** @var \Illuminate\Support\Collection|\App\Models\Peserta[] $peserta */
@endphp

@section('content')
    @php
        $serverNow   = \Carbon\Carbon::now();
        $pulangToday = null;

        if (!empty($materi->jadwal_pulang ?? null)) {
            $raw = trim($materi->jadwal_pulang);
            try {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $raw)) {
                    $p = \Carbon\Carbon::createFromFormat('H:i:s', $raw, $serverNow->timezone);
                } elseif (preg_match('/^\d{2}:\d{2}$/', $raw)) {
                    $p = \Carbon\Carbon::createFromFormat('H:i', $raw, $serverNow->timezone);
                } else {
                    $p = \Carbon\Carbon::parse($raw, $serverNow->timezone);
                }
                $pulangToday = $p->copy()->setDate($serverNow->year, $serverNow->month, $serverNow->day);
            } catch (\Throwable $e) {
                $pulangToday = null;
            }
        }

        $serverNowMs = $serverNow->timestamp * 1000;
        $pulangMs    = $pulangToday ? $pulangToday->timestamp * 1000 : '';
    @endphp

    <div class="min-h-screen bg-gradient-to-br py-4">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4 mb-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="ri-rfid-line text-xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Scan Absensi RFID</h1>
                            <p class="text-gray-600 text-sm">Tempelkan kartu RFID</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Mode Toggle -->
                        <div class="flex items-center gap-2">
                            <button type="button" data-mode="masuk"
                                class="mode-btn px-3 py-1 text-xs rounded border border-gray-300 hover:bg-gray-100">Masuk</button>
                            <button type="button" data-mode="pulang"
                                class="mode-btn px-3 py-1 text-xs rounded border border-gray-300 hover:bg-gray-100">Pulang</button>
                        </div>

                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white min-w-[250px]">
                            <div class="text-sm font-bold opacity-90 text-center">{{ $materi->nama }}</div>
                            <div class="text-xs opacity-90 text-center mt-1">
                                Jam Pulang:
                                <span class="font-semibold">
                                    {{ $pulangToday ? $pulangToday->format('H:i') : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Area Pemindaian -->
                <div class="lg:col-span-1">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 p-4">
                        <h2 class="text-lg font-bold text-gray-900 mb-3 text-center">Area Pemindaian</h2>

                        <div class="relative bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-2xl p-6 border-2 border-dashed border-gray-300 hover:border-blue-400 transition-all duration-300">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="relative">
                                    <div class="absolute inset-0 w-16 h-16 bg-blue-400/20 rounded-full animate-ping"></div>
                                    <div class="relative w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                        <i class="ri-rfid-line text-2xl text-white"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-lg font-bold text-gray-900">Siap Memindai</h3>
                                    <p class="text-gray-600 text-sm">Tempelkan kartu RFID</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs text-gray-600">Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            @php
                                $total       = $peserta->count();
                                $hadir       = $peserta->filter(fn($p) => optional($p->absensi->first())->status === 'hadir')->count();
                                $terlambat   = $peserta->filter(fn($p) => optional($p->absensi->first())->status === 'terlambat')->count();
                                $belum_absen = $peserta->filter(function ($p) {
                                    $a = $p->absensi->first();
                                    return !$a || $a->status === 'belum_absen';
                                })->count();
                            @endphp

                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-gray-900">{{ $total }}</div>
                                <div class="text-xs text-gray-600">Total</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-green-600">{{ $hadir }}</div>
                                <div class="text-xs text-gray-600">Hadir</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-yellow-600">{{ $terlambat }}</div>
                                <div class="text-xs text-gray-600">Terlambat</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-gray-500">{{ $belum_absen }}</div>
                                <div class="text-xs text-gray-600">Belum</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Kehadiran -->
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200/50 bg-gradient-to-r from-gray-50 to-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">Daftar Kehadiran</h3>
                        </div>

                        <div class="overflow-x-auto max-h-96 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">No</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">RFID</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">Nama</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">Kelas</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">Jam Datang</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">Jam Pulang</th>
                                        <th class="py-2 px-3 text-left text-xs font-semibold text-gray-900 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($peserta as $index => $p)
                                        @php
                                            $attendance = $p->absensi->first();
                                            $status     = $attendance?->status ?? 'belum_absen';
                                            $jamDatang  = $attendance?->waktu_datang
                                                ? \Carbon\Carbon::parse($attendance->waktu_datang)->timezone(config('app.timezone'))->format('H:i')
                                                : '—';
                                            $jamPulang  = $attendance?->waktu_pulang
                                                ? \Carbon\Carbon::parse($attendance->waktu_pulang)->timezone(config('app.timezone'))->format('H:i')
                                                : '—';
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                            <td class="py-2 px-3 text-xs text-gray-900">{{ $index + 1 }}</td>
                                            <td class="py-2 px-3 text-xs font-mono text-gray-900 bg-gray-50/30 rounded">{{ $p->id_rfid }}</td>
                                            <td class="py-2 px-3 text-xs font-medium text-gray-900">{{ $p->nama }}</td>
                                            <td class="py-2 px-3 text-xs text-gray-500">{{ $p->kelas ?? '—' }}</td>
                                            <td class="py-2 px-3 text-xs text-gray-900">{{ $jamDatang }}</td>
                                            <td class="py-2 px-3 text-xs text-gray-900">{{ $jamPulang }}</td>
                                            <td class="py-2 px-3 text-xs">
                                                <span
                                                    class="px-2 py-1 inline-flex items-center text-xs leading-4 font-semibold rounded-full
                                                    @if ($status === 'hadir') bg-green-100 text-green-800
                                                    @elseif($status === 'terlambat') bg-yellow-100 text-yellow-800
                                                    @elseif($status === 'tidak_hadir') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if ($status === 'hadir')
                                                        <i class="ri-check-line mr-1"></i>Hadir
                                                    @elseif($status === 'terlambat')
                                                        <i class="ri-time-line mr-1"></i>Terlambat
                                                    @elseif($status === 'tidak_hadir')
                                                        <i class="ri-close-line mr-1"></i>Tidak Hadir
                                                    @else
                                                        <i class="ri-question-line mr-1"></i>Belum
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success') || session('error'))
                <div id="statusModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl transform animate-bounce">
                        <div class="text-center">
                            @if (session('success'))
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 mb-4">
                                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-3">Absensi Berhasil!</h3>
                                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-4">
                                    {{ session('success') }}
                                </div>
                            @elseif(session('error'))
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-red-400 to-rose-500 mb-4">
                                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-3">Absensi Gagal!</h3>
                                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <button type="button" onclick="closeModal()
                                " class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- (Opsional) Fallback audio kalau Web Speech API tidak tersedia --}}
            <audio id="voice-fallback" src="{{ asset('audio/selamat-belajar.mp3') }}" preload="auto"></audio>

            <!-- Hidden Form -->
            <form method="POST" action="{{ route('absensi.store') }}" id="rfid_form"
                style="opacity: 0; position: absolute; left: -9999px;">
                @csrf
                <input type="hidden" name="materi_id" value="{{ $materi->id ?? '' }}">
                <input type="hidden" id="mode_input" name="mode" value="masuk">
                <input type="text" id="rfid_input" name="id_rfid" required autocomplete="off">
                <button type="submit" id="hidden_submit"></button>

                <input type="hidden" id="server_now_ts" value="{{ $serverNowMs }}">
                <input type="hidden" id="pulang_ts" value="{{ $pulangMs }}">
            </form>

            <!-- Visual Feedback -->
            <div id="scan_feedback" class="fixed inset-0 flex items-center justify-center z-40 bg-black/50 backdrop-blur-sm hidden">
                <div class="bg-white rounded-2xl p-6 max-w-xs w-full mx-4 shadow-2xl">
                    <div class="text-center">
                        <div class="relative w-16 h-16 mx-auto mb-4">
                            <div class="absolute inset-0 bg-blue-400/20 rounded-full animate-ping"></div>
                            <div class="relative w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                <i class="ri-loader-4-line text-2xl text-white animate-spin"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Memproses...</h3>
                        <p class="text-gray-600 text-sm">Sedang memproses kartu RFID</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce { 0%,20%,53%,80%,100%{transform:translate3d(0,0,0)}40%,43%{transform:translate3d(0,-15px,0)}70%{transform:translate3d(0,-7px,0)}90%{transform:translate3d(0,-2px,0)} }
        .animate-bounce { animation: bounce 0.6s ease-in-out; }
    </style>

    <script>
  document.addEventListener('DOMContentLoaded', function() {
      const rfidInput    = document.getElementById('rfid_input');
      const scanFeedback = document.getElementById('scan_feedback');
      const rfidForm     = document.getElementById('rfid_form');
      const modeInput    = document.getElementById('mode_input');

      function setActiveModeButton(mode) {
          document.querySelectorAll('.mode-btn').forEach(b => {
              b.classList.remove('bg-blue-600','text-white');
              if (b.dataset.mode === mode) b.classList.add('bg-blue-600','text-white');
          });
          modeInput.value = mode;
      }

      // === Ambil serverNow & jadwal pulang dari hidden input ===
      let serverNow = Number(document.getElementById('server_now_ts').value || Date.now());
      const pulangTsStr = document.getElementById('pulang_ts').value;
      const pulangTs = pulangTsStr ? Number(pulangTsStr) : NaN;

      // Default mode otomatis sesuai jam
      if (pulangTs && !isNaN(pulangTs) && serverNow >= pulangTs) {
          // Kalau sudah lewat jam pulang → default pulang
          setActiveModeButton('pulang');
      } else {
          // Kalau masih pagi → default masuk
          setActiveModeButton('masuk');
      }

      // Manual toggle tetap bisa
      document.querySelectorAll('.mode-btn').forEach(btn => {
          btn.addEventListener('click', () => {
              setActiveModeButton(btn.dataset.mode);
              rfidInput.focus();
          });
      });

      rfidInput.focus();
      let submitting = false;
      let timer = null;

      // Tambahin detik supaya serverNow tetap jalan
      setInterval(() => { serverNow += 1000; }, 1000);

      function blockIfBeforePulang() {
          if (modeInput.value !== 'pulang') return null;
          if (!pulangTs || isNaN(pulangTs)) return null;
          if (serverNow < pulangTs) {
              const jam = new Date(pulangTs).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
              return `Belum waktu pulang. Jadwal pulang: ${jam}`;
          }
          return null;
      }

      function submitOnce() {
          if (submitting) return;
          const blockMsg = blockIfBeforePulang();
          if (blockMsg) { alert(blockMsg); return; }
          submitting = true;
          scanFeedback.classList.remove('hidden');
          rfidForm.requestSubmit();
          setTimeout(() => { submitting = false; }, 1500);
      }

      function handleRfidScan(value) {
          if (value && value.trim().length >= 8) {
              clearTimeout(timer);
              timer = setTimeout(submitOnce, 250);
          }
      }

      rfidInput.addEventListener('input', (e) => handleRfidScan(e.target.value));
      rfidInput.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
              e.preventDefault();
              handleRfidScan(rfidInput.value);
          }
      });

      rfidForm.addEventListener('submit', (e) => {
          if (!rfidInput.value || rfidInput.value.trim() === '') {
              e.preventDefault();
              return;
          }
          setTimeout(() => { rfidInput.value = ''; }, 50);
      });

      window.addEventListener('beforeunload', function() {
          rfidInput.value = '';
      });
  });

  @if (session('success') || session('error'))
      setTimeout(() => { closeModal(); }, 2000);
      function closeModal() {
          const el = document.getElementById('statusModal');
          if (el) el.classList.add('hidden');
      }
  @endif
</script>

{{-- ====== TTS: Masuk & Pulang ====== --}}
<script>
  (function () {
    const modal = document.getElementById('statusModal');
    if (!modal) return;

    // Hanya bicara jika Sukses
    const successBox = modal.querySelector('.bg-green-50');
    if (!successBox) return;

    const raw = (successBox.textContent || '').trim();

    // Deteksi mode (MASUK / PULANG) dan nama
    let mode = ''; // 'MASUK' | 'PULANG'
    let nama = '';

    // Pola umum: "ABSEN MASUK: Daniel Sinaga @ 07:31"
    let m = raw.match(/ABSEN\s+(MASUK|PULANG)\s*:\s*([^@]+?)(?:@|$)/i);
    if (m) {
      mode = (m[1] || '').toUpperCase().trim();
      nama = (m[2] || '').trim();
    }

    // Kalau belum dapat nama, coba "Berhasil: Daniel Sinaga"
    if (!nama) {
      m = raw.match(/Berhasil\s*:\s*(.+)$/i);
      if (m) nama = m[1].trim();
    }

    // Terakhir: ambil kata kapital beruntun (Nama Depan Belakang)
    if (!nama) {
      m = raw.match(/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/);
      if (m) nama = m[1].trim();
    }

    // Susun kalimat sesuai mode
    let kalimat = 'Selamat belajar';
    if (mode === 'PULANG') {
      kalimat = 'Terima kasih, hati-hati di jalan';
    }
    if (nama) kalimat += ' ' + nama;

    speak(kalimat);
  })();

  function speak(text) {
    try {
      const synth = window.speechSynthesis;
      if (!synth) return;

      const u = new SpeechSynthesisUtterance(text);
      const pilihSuara = () => {
        const voices = synth.getVoices();
        u.voice = voices.find(v => /id-ID/i.test(v.lang))
              || voices.find(v => /Indones/i.test(v.name))
              || voices[0] || null;
        u.lang = (u.voice && u.voice.lang) || 'id-ID';
        u.rate = 0.95;
        u.pitch = 1;
        u.volume = 1;
        synth.cancel();
        synth.speak(u);
      };

      if (synth.getVoices().length) pilihSuara();
      else synth.onvoiceschanged = pilihSuara;

      // Beberapa browser butuh interaksi user sekali agar audio jalan
      document.addEventListener('click', function resumeOnce() {
        try { speechSynthesis.resume(); } catch (e) {}
        document.removeEventListener('click', resumeOnce);
      });
    } catch (e) {
      // no-op jika TTS tidak didukung
    }
  }
</script>

@endsection
