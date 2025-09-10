<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Peserta;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $kelasFilter = $request->query('kelas');

        $peserta = Peserta::with('account')
            ->where('user_id', Auth::id())
            ->when($kelasFilter, fn ($q) => $q->where('kelas', $kelasFilter))
            ->orderBy('nama')
            ->paginate(20)
            ->appends(['kelas' => $kelasFilter]);

        $daftarKelas = Peserta::where('user_id', Auth::id())
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->values();

        return view('peserta.index', [
            'peserta'     => $peserta,
            'daftarKelas' => $daftarKelas,
            'kelasFilter' => $kelasFilter,
        ]);
    }

    /** Ambil daftar jadwal (id & nama) dari kolom yang tersedia. */
    protected function ambilJadwalUntukForm()
    {
        // Prioritaskan nama_jadwal (sesuai tampilan index jadwal)
        $kolomKandidat = ['nama_jadwal', 'nama', 'judul', 'kelas'];

        foreach ($kolomKandidat as $kolom) {
            try {
                // ⚠️ Tanpa filter user_id
                return DB::table('jadwals')
                    ->orderBy($kolom)
                    ->get(['id', DB::raw("$kolom as nama")]);
            } catch (\Throwable $e) {
                // lanjut ke kolom berikutnya
            }
        }

        return collect();
    }

    /** Cari id jadwal berdasarkan nama kelas (untuk preselect di edit). */
    protected function cariJadwalIdDariNamaKelas(string $namaKelas = null)
    {
        if (!$namaKelas) return null;
        $items = $this->ambilJadwalUntukForm();
        $match = $items->firstWhere('nama', $namaKelas);
        return $match->id ?? null;
    }

    public function create()
    {
        $daftarKelas = Peserta::where('user_id', Auth::id())
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->values();

        $daftarJadwal = $this->ambilJadwalUntukForm();

        return view('peserta.create', compact('daftarKelas', 'daftarJadwal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rfid'        => 'required|string|max:20|unique:peserta,id_rfid',
            'nama'           => 'required|string|max:255',
            'jadwal_id'      => 'required|exists:jadwals,id',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_daftar' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'jadwal_id.required' => 'Jadwal kelas wajib dipilih.',
            'jadwal_id.exists'   => 'Jadwal tidak ditemukan.',
        ]);

        // ⚠️ Tanpa filter user_id
        $jadwal = Jadwal::findOrFail($data['jadwal_id']);

        // Tentukan nama kelas dari kolom yang tersedia
        $namaKelas = $jadwal->nama_jadwal
            ?? $jadwal->nama
            ?? $jadwal->judul
            ?? $jadwal->kelas
            ?? 'Kelas';

        $data['id_rfid']        = (string) $data['id_rfid'];
        $data['user_id']        = Auth::id();
        $data['kelas']          = $namaKelas;
        $data['tanggal_daftar'] = $request->filled('tanggal_daftar')
            ? Carbon::parse($request->tanggal_daftar)->startOfDay()
            : Carbon::today();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_peserta', 'public');
        }

        // Tidak menyimpan jadwal_id ke tabel peserta (hanya menyimpan nama kelas)
        unset($data['jadwal_id']);

        Peserta::create($data);

        return redirect()->route('peserta.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function show($id)
    {
        $peserta = Peserta::with('account')->where('user_id', Auth::id())->findOrFail($id);
        return view('peserta.show', compact('peserta'));
    }

    public function edit($id)
    {
        $peserta = Peserta::where('user_id', Auth::id())->findOrFail($id);

        $daftarKelas = Peserta::where('user_id', Auth::id())
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->values();

        // >>>>>>> INI YANG DITAMBAHKAN: kirim daftarJadwal ke view
        $daftarJadwal = $this->ambilJadwalUntukForm();

        // Opsional: preselect jadwal berdasarkan nama kelas yang tersimpan
        $selectedJadwalId = $this->cariJadwalIdDariNamaKelas($peserta->kelas);

        return view('peserta.edit', compact('peserta', 'daftarKelas', 'daftarJadwal', 'selectedJadwalId'));
    }

    public function update(Request $request, $id)
    {
        $peserta = Peserta::where('user_id', Auth::id())->findOrFail($id);

        // Di edit kita pakai jadwal_id, dan kelas akan ditentukan oleh server
        $data = $request->validate([
            'id_rfid'        => 'required|string|max:20|unique:peserta,id_rfid,' . $peserta->id,
            'nama'           => 'required|string|max:255',
            'jadwal_id'      => 'required|exists:jadwals,id',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_daftar' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'jadwal_id.required' => 'Jadwal kelas wajib dipilih.',
            'jadwal_id.exists'   => 'Jadwal tidak ditemukan.',
        ]);

        $data['id_rfid'] = (string) $data['id_rfid'];
        $data['tanggal_daftar'] = $request->filled('tanggal_daftar')
            ? Carbon::parse($request->tanggal_daftar)->startOfDay()
            : $peserta->tanggal_daftar;

        // Set kelas dari jadwal yang dipilih
        $jadwal = Jadwal::findOrFail($data['jadwal_id']);
        $data['kelas'] = $jadwal->nama_jadwal
            ?? $jadwal->nama
            ?? $jadwal->judul
            ?? $jadwal->kelas
            ?? $peserta->kelas;

        // Hapus jadwal_id agar tidak disimpan di tabel peserta
        unset($data['jadwal_id']);

        if ($request->hasFile('foto')) {
            if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
                Storage::disk('public')->delete($peserta->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_peserta', 'public');
        }

        $peserta->update($data);

        return redirect()->route('peserta.index')->with('success', 'Murid berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $peserta = Peserta::where('user_id', Auth::id())->findOrFail($id);

        if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
            Storage::disk('public')->delete($peserta->foto);
        }

        $peserta->delete();

        return redirect()->route('peserta.index')->with('success', 'Murid berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $kelasFilter = $request->query('kelas');

        $peserta = Peserta::where('user_id', Auth::id())
            ->when($kelasFilter, fn ($q) => $q->where('kelas', $kelasFilter))
            ->orderBy('nama')
            ->get();

        $currentDateTime = Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        $pdf = Pdf::loadView('peserta.export', [
            'peserta'         => $peserta,
            'currentDateTime' => $currentDateTime,
            'kelasFilter'     => $kelasFilter,
        ]);

        $namaFile = 'daftar_murid' . ($kelasFilter ? "_{$kelasFilter}" : '') . '.pdf';
        return $pdf->download($namaFile);
    }
}
