<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $kelasFilter = $request->query('kelas');

        $peserta = Peserta::with('account') // eager-load akun agar kolom "Akun Siswa" jalan tanpa N+1
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

    public function create()
    {
        $daftarKelas = Peserta::where('user_id', Auth::id())
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->values();

        return view('peserta.create', compact('daftarKelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_rfid'        => 'required|string|max:20|unique:peserta,id_rfid',
            'nama'           => 'required|string|max:255',
            'kelas'          => 'required|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_daftar' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['id_rfid'] = (string) $data['id_rfid'];
        $data['user_id'] = Auth::id();
        $data['tanggal_daftar'] = $request->filled('tanggal_daftar')
            ? Carbon::parse($request->tanggal_daftar)->startOfDay()
            : Carbon::today();

        // Upload foto kalau ada
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_peserta', 'public');
            $data['foto'] = $path;
        }

        Peserta::create($data);

        return redirect()->route('peserta.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function show($id)
    {
        // TAMPIL DETAIL MURID + RELASI AKUN
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

        return view('peserta.edit', compact('peserta', 'daftarKelas'));
    }

    public function update(Request $request, $id)
    {
        $peserta = Peserta::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'id_rfid'        => 'required|string|max:20|unique:peserta,id_rfid,' . $peserta->id,
            'nama'           => 'required|string|max:255',
            'kelas'          => 'required|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_daftar' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['id_rfid'] = (string) $data['id_rfid'];
        $data['tanggal_daftar'] = $request->filled('tanggal_daftar')
            ? Carbon::parse($request->tanggal_daftar)->startOfDay()
            : $peserta->tanggal_daftar;

        // Upload ulang foto kalau ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
                Storage::disk('public')->delete($peserta->foto);
            }
            $path = $request->file('foto')->store('foto_peserta', 'public');
            $data['foto'] = $path;
        }

        $peserta->update($data);

        return redirect()->route('peserta.index')->with('success', 'Murid berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $peserta = Peserta::where('user_id', Auth::id())->findOrFail($id);

        // Hapus foto juga kalau ada
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
