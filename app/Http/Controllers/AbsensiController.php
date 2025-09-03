<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Absensi;
use App\Models\Peserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $materis = Materi::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('absensi.index', compact('materis'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_rfid'   => 'required|string',
            'materi_id' => 'required|exists:materis,id',
            'mode'      => 'required|in:masuk,pulang',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        // Pastikan materi milik user
        $materi = Materi::where('user_id', Auth::id())->findOrFail($request->materi_id);

        // Cari murid berdasarkan RFID & milik user
        $peserta = Peserta::where('id_rfid', $request->id_rfid)
            ->where('user_id', Auth::id())
            ->first();

        if (!$peserta) {
            return back()->with('error', 'Murid tidak ditemukan dengan RFID tersebut.');
        }

        $now = Carbon::now(); // timezone mengikuti config/app.php

        // Toleransi keterlambatan dari config
        $graceMinutes = (int) config('absensi.grace_minutes', 10);

        // Hitung status hadir/terlambat dari jadwal_mulai
        $mulai       = $this->parseTodayTime($materi->jadwal_mulai, $now) ?? $now->copy();
        $statusMasuk = $now->lte($mulai->copy()->addMinutes($graceMinutes)) ? 'hadir' : 'terlambat';

        // Jadwal pulang hari ini
        $pulangToday = $this->parseTodayTime($materi->jadwal_pulang, $now);

        try {
            DB::beginTransaction();

            $absen = Absensi::where('peserta_id', $peserta->id)
                ->where('materi_id', $materi->id)
                ->first();

            $mode = $request->input('mode'); // 'masuk' | 'pulang'

            // ================= MODE MASUK =================
            if ($mode === 'masuk') {
                if ($absen && !empty($absen->waktu_datang)) {
                    DB::rollBack();
                    return back()->with('error', 'Sudah tercatat jam masuk untuk murid ini.');
                }

                if (!$absen) {
                    Absensi::create([
                        'user_id'      => Auth::id(), // hapus jika tabel absensi tidak punya kolom user_id
                        'peserta_id'   => $peserta->id,
                        'materi_id'    => $materi->id,
                        'status'       => $statusMasuk,
                        'waktu_datang' => $now,
                    ]);
                } else {
                    $absen->waktu_datang = $now;
                    if (empty($absen->status) || $absen->status === 'belum_absen') {
                        $absen->status = $statusMasuk;
                    }
                    $absen->save();
                }

                DB::commit();
                return back()->with('success', "ABSEN MASUK: {$peserta->nama} @ " . $now->format('H:i') . " (Status: {$statusMasuk})");
            }

            // ================= MODE PULANG =================
            if ($mode === 'pulang') {
                if (!$absen || empty($absen->waktu_datang)) {
                    DB::rollBack();
                    return back()->with('error', 'Belum ada absensi masuk. Scan masuk dulu.');
                }

                if (!empty($absen->waktu_pulang)) {
                    DB::rollBack();
                    return back()->with('error', 'Sudah tercatat jam pulang.');
                }

                if (!$pulangToday) {
                    DB::rollBack();
                    return back()->with('error', 'Jadwal pulang belum ditentukan pada absen harian ini.');
                }

                if ($now->lt($pulangToday)) {
                    DB::rollBack();
                    return back()->with('error', 'Belum waktu pulang sesuai jadwal.');
                }

                $absen->waktu_pulang = $now;
                $absen->save();

                DB::commit();
                return back()->with('success', "ABSEN PULANG: {$peserta->nama} @ " . $now->format('H:i'));
            }

            DB::rollBack();
            return back()->with('error', 'Mode tidak dikenali.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat absensi: ' . $e->getMessage());
        }
    }

    public function scan($materiId)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($materiId);

        // Ambil peserta milik user—coba filter sesuai kelas materi->komisi; fallback ke semua jika kosong.
        $query = Peserta::where('user_id', Auth::id())
            ->select('id', 'user_id', 'id_rfid', 'nama', 'kelas')
            ->with([
                'absensi' => function ($q) use ($materiId) {
                    $q->where('materi_id', $materiId);
                }
            ])
            ->orderBy('nama');

        if (!empty($materi->komisi)) {
            $peserta = (clone $query)->where('kelas', $materi->komisi)->get();
            if ($peserta->isEmpty()) {
                $peserta = (clone $query)->get();
            }
        } else {
            $peserta = $query->get();
        }

        return view('absensi.scan', compact('materi', 'peserta'));
    }

    public function export($materiId)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($materiId);

        $query = Peserta::where('user_id', Auth::id())
            ->with(['absensi' => function ($q) use ($materiId) {
                $q->where('materi_id', $materiId);
            }])
            ->orderBy('nama');

        if (!empty($materi->komisi)) {
            $query->where('kelas', $materi->komisi);
        }

        $peserta = $query->get();

        $currentDateTime = Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';
        $pdf = Pdf::loadView('absensi.export', compact('materi', 'peserta', 'currentDateTime'));

        return $pdf->download('absensi_' . $materi->nama . '.pdf');
    }

    /**
     * Parse string waktu (H:i atau H:i:s) menjadi Carbon pada tanggal HARI INI.
     * Gagal parse => null.
     */
    private function parseTodayTime(?string $timeStr, Carbon $now): ?Carbon
    {
        if (!$timeStr) return null;

        $raw = trim($timeStr);
        try {
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $raw)) {
                $t = Carbon::createFromFormat('H:i:s', $raw, $now->timezone);
            } elseif (preg_match('/^\d{2}:\d{2}$/', $raw)) {
                $t = Carbon::createFromFormat('H:i', $raw, $now->timezone);
            } else {
                $t = Carbon::parse($raw, $now->timezone);
            }
            return $t->setDate($now->year, $now->month, $now->day);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
