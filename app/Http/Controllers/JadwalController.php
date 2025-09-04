<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\JadwalDetail;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::withCount('details')->orderBy('nama_jadwal')->get();
        return view('jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        return view('jadwal.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_jadwal'     => 'required|string|max:255',
            'hari'            => 'required|array',
            'hari.*'          => 'required|string',
            'mapel'           => 'required|array',
            'mapel.*'         => 'required|string',
            'jam_mulai'       => 'required|array',
            'jam_mulai.*'     => 'required',
            'jam_selesai'     => 'required|array',
            'jam_selesai.*'   => 'required',
        ]);

        $jadwal = Jadwal::create(['nama_jadwal' => $data['nama_jadwal']]);

        foreach ($data['hari'] as $i => $hari) {
            JadwalDetail::create([
                'jadwal_id'   => $jadwal->id,
                'hari'        => $hari,
                'mapel'       => $data['mapel'][$i],
                'jam_mulai'   => $data['jam_mulai'][$i],
                'jam_selesai' => $data['jam_selesai'][$i],
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['details' => function($q){
            $q->orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat')")
              ->orderBy('jam_mulai');
        }]);

        return view('jadwal.show', compact('jadwal'));
    }

    public function edit(Jadwal $jadwal)
    {
        $jadwal->load('details');
        return view('jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $data = $request->validate([
            'nama_jadwal'     => 'required|string|max:255',
            'hari'            => 'required|array',
            'hari.*'          => 'required|string',
            'mapel'           => 'required|array',
            'mapel.*'         => 'required|string',
            'jam_mulai'       => 'required|array',
            'jam_mulai.*'     => 'required',
            'jam_selesai'     => 'required|array',
            'jam_selesai.*'   => 'required',
        ]);

        $jadwal->update(['nama_jadwal' => $data['nama_jadwal']]);

        // simple strategy: replace all details
        $jadwal->details()->delete();
        foreach ($data['hari'] as $i => $hari) {
            JadwalDetail::create([
                'jadwal_id'   => $jadwal->id,
                'hari'        => $hari,
                'mapel'       => $data['mapel'][$i],
                'jam_mulai'   => $data['jam_mulai'][$i],
                'jam_selesai' => $data['jam_selesai'][$i],
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diupdate.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
