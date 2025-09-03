<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Materi;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ========= Ringkasan =========
        $totalPeserta = Peserta::where('user_id', $userId)->count();
        $totalMateri  = Materi::where('user_id', $userId)->count();
        $totalAbsensi = Absensi::where('user_id', $userId)->count();

        // ========= Absensi terbaru (5 terakhir) =========
        $recentAttendance = Absensi::with([
                'peserta:id,nama,kelas',
                'materi:id,nama',
            ])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ========= Statistik per kelas =========
        $statsKelas = Peserta::where('user_id', $userId)
            ->select('kelas', DB::raw('COUNT(*) AS jumlah'))
            ->whereNotNull('kelas')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        // ========= Chart: total kehadiran status 'hadir' per materi =========
        $attendanceByMateri = Absensi::select(
                'materis.id',
                'materis.nama as materi_nama',
                DB::raw('COUNT(absensi.id) as total_kehadiran')
            )
            ->join('materis', 'absensi.materi_id', '=', 'materis.id')
            ->where('absensi.user_id', $userId)
            ->where('absensi.status', 'hadir')
            ->groupBy('materis.id', 'materis.nama')
            ->orderBy('materis.nama')
            ->get();

        $chartLabels = $attendanceByMateri->pluck('materi_nama')->values();
        $chartData   = $attendanceByMateri->pluck('total_kehadiran')->values();

        // ========= Grafik Kehadiran per Hari per Status (Senin–Jumat minggu ini) =========
        $hariLabels = ['Senin','Selasa','Rabu','Kamis','Jumat'];
        $hariDataStatus = [
            'hadir'       => array_fill(0, count($hariLabels), 0),
            'terlambat'   => array_fill(0, count($hariLabels), 0),
            'tidak_hadir' => array_fill(0, count($hariLabels), 0),
        ];

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::FRIDAY); // hanya Senin–Jumat

        $absensiPerHariStatus = Absensi::select(
                DB::raw('DAYOFWEEK(created_at) as hari'),
                'status',
                DB::raw('count(*) as total')
            )
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('hari','status')
            ->get();

        foreach ($absensiPerHariStatus as $row) {
            // MySQL DAYOFWEEK: Minggu=1, Senin=2, ..., Sabtu=7
            $index = $row->hari - 2; // Senin=0, Selasa=1, ... Jumat=4
            if ($index >= 0 && $index < 5) {
                $status = strtolower($row->status);
                if (isset($hariDataStatus[$status][$index])) {
                    $hariDataStatus[$status][$index] = $row->total;
                }
            }
        }

        // ========= Filter kelas untuk "Kehadiran Hari Ini" =========
        $filterKelas = $request->get('kelas'); // dari query string ?kelas=...

        // List kelas unik (untuk dropdown filter di view)
        $kelasList = Peserta::where('user_id', $userId)
            ->select('kelas')
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        // Semua siswa + absensi hari ini, dengan filter kelas opsional
        $today = Carbon::today();
        $allPesertaWithAbsensi = Peserta::where('user_id', $userId)
            ->when($filterKelas, function ($q) use ($filterKelas) {
                $q->where('kelas', $filterKelas);
            })
            ->with(['absensi' => function($q) use ($today) {
                $q->whereDate('created_at', $today);
            }])
            ->orderBy('nama')
            ->get();

        return view('dashboard', compact(
            'totalPeserta',
            'totalMateri',
            'totalAbsensi',
            'recentAttendance',
            'statsKelas',
            'chartLabels',
            'chartData',
            'hariLabels',
            'hariDataStatus',
            'allPesertaWithAbsensi',
            'kelasList',
            'filterKelas'
        ));
    }
}
