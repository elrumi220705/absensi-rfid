<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SiswaJadwalController extends Controller
{
    public function index(Request $request)
    {
        try {
            /** @var \App\Models\SiswaAccount $account */
            $account = $request->user()->load('peserta');
            $kelas   = optional($account->peserta)->kelas;

            if (!$kelas) {
                return response()->json([
                    'kelas' => null,
                    'items' => [],
                    'days'  => [],
                ], 200);
            }

            $target = mb_strtolower(trim($kelas));

            // Bangun kueri HANYA dengan kolom yang benar-benar ada di tabel
            $cols = ['nama_jadwal', 'nama', 'judul', 'kelas']; // kandidat
            $available = array_values(array_filter($cols, fn($c) => Schema::hasColumn('jadwals', $c)));

            // Fallback kalau hanya kolom nama_jadwal yang ada (kasus umum)
            if (empty($available)) {
                // kalau sampai sini, kita tetap coba nama_jadwal (andai Schema cache salah)
                $available = ['nama_jadwal'];
            }

            $q = Jadwal::query();
            $first = true;
            foreach ($available as $col) {
                // LOWER(col) = ? (case-insensitive)
                $expr = "LOWER($col) = ?";
                if ($first) {
                    $q->whereRaw($expr, [$target]);
                    $first = false;
                } else {
                    $q->orWhereRaw($expr, [$target]);
                }
            }
            $jadwal = $q->first();

            if (!$jadwal) {
                return response()->json([
                    'kelas' => $kelas,
                    'items' => [],
                    'days'  => [],
                ], 200);
            }

            // Ambil detail & normalisasi hari
            $details = $jadwal->details()
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get(['hari','mapel','jam_mulai','jam_selesai']);

            $normalize = function (?string $h) {
                $s = Str::of((string)$h)->lower()->trim();
                return match (true) {
                    $s->contains('selasa')   => 'Selasa',
                    $s->contains('rabu')     => 'Rabu',
                    $s->contains('kamis')    => 'Kamis',
                    $s->contains('jumat'),
                    $s->contains('jum\'at'),
                    $s->contains('jum’at')   => 'Jumat',
                    $s->contains('sabtu')    => 'Sabtu',
                    default                  => 'Senin',
                };
            };

            $items = [];
            foreach ($details as $d) {
                $items[] = [
                    'hari'        => $normalize($d->hari),
                    'mapel'       => (string) $d->mapel,
                    'jam_mulai'   => substr((string) $d->jam_mulai, 0, 5),
                    'jam_selesai' => substr((string) $d->jam_selesai, 0, 5),
                ];
            }

            // Urutkan: hari (Senin..Sabtu) lalu jam mulai
            $orderDays = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $dayIndex  = array_flip($orderDays);
            usort($items, function ($a, $b) use ($dayIndex) {
                $da = $dayIndex[$a['hari']] ?? 0;
                $db = $dayIndex[$b['hari']] ?? 0;
                if ($da !== $db) return $da <=> $db;
                return str_replace(':','',$a['jam_mulai']) <=> str_replace(':','',$b['jam_mulai']);
            });

            // Kelompokkan per hari
            $bucket = [];
            foreach ($orderDays as $d) $bucket[$d] = [];
            foreach ($items as $it) {
                $bucket[$it['hari']][] = [
                    'mapel'       => $it['mapel'],
                    'jam_mulai'   => $it['jam_mulai'],
                    'jam_selesai' => $it['jam_selesai'],
                ];
            }
            $days = [];
            foreach ($orderDays as $d) {
                $days[] = ['hari' => $d, 'items' => $bucket[$d]];
            }

            return response()->json([
                'kelas' => $kelas,
                'items' => $items,
                'days'  => $days,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('API siswa/jadwal error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'kelas'   => null,
                'items'   => [],
                'days'    => [],
                'message' => 'Gagal memuat jadwal.',
            ], 200);
        }
    }
}
