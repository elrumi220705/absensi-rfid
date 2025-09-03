<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MateriController extends Controller
{
    public function index()
    {
        // Tampilkan terbaru di atas
        $materis = Materi::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('materi.index', compact('materis'));
    }

    public function create()
    {
        // Tidak perlu daftar kelas lagi
        return view('materi.create');
    }

    public function store(Request $request)
    {
        // Nama boleh kosong → akan diisi otomatis (ddmmyy)
        $request->validate([
            'nama'          => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'jadwal_mulai'  => 'required|date_format:H:i',
            'jadwal_pulang' => 'required|date_format:H:i|after:jadwal_mulai',
        ]);

        $nama = trim((string) $request->input('nama'));
        if ($nama === '') {
            $nama = Carbon::now()->format('dmy'); // contoh: 150825
        }

        $mulai  = $this->toHms($request->input('jadwal_mulai'));  // H:i -> H:i:s
        $pulang = $this->toHms($request->input('jadwal_pulang')); // H:i -> H:i:s

        Materi::create([
            'user_id'       => Auth::id(),
            'nama'          => $nama,
            'deskripsi'     => $request->input('deskripsi'),
            'jadwal_mulai'  => $mulai,
            'jadwal_pulang' => $pulang,
        ]);

        return redirect()
            ->route('materi.index')
            ->with('success', 'Absen harian berhasil dibuat.');
    }

    public function show($id)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($id);
        return view('materi.show', compact('materi'));
    }

    public function edit($id)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($id);
        // Tidak perlu kelasList
        return view('materi.edit', compact('materi'));
    }

    public function update(Request $request, $id)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama'          => 'required|string|max:255', // saat edit, nama wajib (sudah ada nilainya)
            'deskripsi'     => 'nullable|string',
            'jadwal_mulai'  => 'required|date_format:H:i',
            'jadwal_pulang' => 'required|date_format:H:i|after:jadwal_mulai',
        ]);

        $materi->update([
            'nama'          => trim($request->input('nama')),
            'deskripsi'     => $request->input('deskripsi'),
            'jadwal_mulai'  => $this->toHms($request->input('jadwal_mulai')),
            'jadwal_pulang' => $this->toHms($request->input('jadwal_pulang')),
        ]);

        return redirect()
            ->route('materi.index')
            ->with('success', 'Absen harian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $materi = Materi::where('user_id', Auth::id())->findOrFail($id);
        $materi->delete();

        return redirect()
            ->route('materi.index')
            ->with('success', 'Absen harian dihapus.');
    }

    /**
     * Ubah "H:i" menjadi "H:i:s" agar aman disimpan di kolom TIME.
     */
    private function toHms(string $val): string
    {
        return Carbon::createFromFormat('H:i', $val)->format('H:i:s');
    }
}
