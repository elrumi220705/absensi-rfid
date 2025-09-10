<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\SiswaAccount;
use Illuminate\Http\Request;

class SiswaAccountController extends Controller
{
    public function store(Request $request, $pesertaId)
    {
        $peserta = Peserta::findOrFail($pesertaId);

        $data = $request->validate([
            'login_id' => 'required|string|unique:siswa_accounts,login_id',
            'password' => 'required|string|min:4',
            'is_active'=> 'nullable|boolean',
        ]);

        $acc = new SiswaAccount([
            'peserta_id' => $peserta->id,
            'login_id'   => $data['login_id'],
            'is_active'  => $data['is_active'] ?? true,
        ]);
        $acc->setPlainPassword($data['password']);
        $acc->save();

        return back()->with('success', 'Akun login siswa berhasil dibuat.');
    }
}
