<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class SiswaAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'login_id'    => 'required|string',
            'password'    => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $account = SiswaAccount::with('peserta')
            ->where('login_id', $data['login_id'])
            ->first();

        if (!$account || !$account->is_active || !$account->checkPassword($data['password'])) {
            throw ValidationException::withMessages([
                'login_id' => ['ID atau password salah.'],
            ]);
        }

        $token = $account->createToken($data['device_name'] ?? 'mobile')->plainTextToken;

        $account->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            'token'   => $token,
            'profile' => [
                'id'       => $account->peserta->id,
                'nama'     => $account->peserta->nama,
                'kelas'    => $account->peserta->kelas,
                'id_rfid'  => $account->peserta->id_rfid,
                'foto_url' => method_exists($account->peserta, 'getFotoUrlAttribute')
                                ? $account->peserta->foto_url
                                : null,
            ],
        ]);
    }

    public function me(Request $request)
    {
        /** @var \App\Models\SiswaAccount $account */
        $account = $request->user();
        $account->load('peserta');

        return response()->json([
            'account' => [
                'login_id'      => $account->login_id,
                'is_active'     => $account->is_active,
                'last_login_at' => $account->last_login_at,
            ],
            'profile' => [
                'id'       => $account->peserta->id,
                'nama'     => $account->peserta->nama,
                'kelas'    => $account->peserta->kelas,
                'id_rfid'  => $account->peserta->id_rfid,
                'foto_url' => method_exists($account->peserta, 'getFotoUrlAttribute')
                                ? $account->peserta->foto_url
                                : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }

    // ====== PROFIL ======
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\SiswaAccount $account */
        $account = $request->user();
        $account->load('peserta');
        $peserta = $account->peserta;

        $data = $request->validate([
            'nama'  => 'required|string|max:255',
            'kelas' => 'nullable|string|max:50',
            'foto'  => ['nullable', File::image()->max('2mb')],
        ]);

        $peserta->nama  = $data['nama'];
        if (array_key_exists('kelas', $data)) {
            $peserta->kelas = $data['kelas'];
        }

        if ($request->hasFile('foto')) {
            if ($peserta->foto && \Storage::disk('public')->exists($peserta->foto)) {
                \Storage::disk('public')->delete($peserta->foto);
            }
            $peserta->foto = $request->file('foto')->store('foto_peserta', 'public');
        }

        $peserta->save();

        return response()->json([
            'profile' => [
                'id'       => $peserta->id,
                'nama'     => $peserta->nama,
                'kelas'    => $peserta->kelas,
                'id_rfid'  => $peserta->id_rfid,
                'foto_url' => method_exists($peserta, 'getFotoUrlAttribute') ? $peserta->foto_url : null,
            ]
        ]);
    }

    // ====== PASSWORD ======
    public function changePassword(Request $request)
    {
        /** @var \App\Models\SiswaAccount $account */
        $account = $request->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $account->password)) {
            return response()->json(['message' => 'Password saat ini salah.'], 422);
        }

        $account->password = Hash::make($data['new_password']);
        $account->save();

        return response()->json(['ok' => true]);
    }
}
