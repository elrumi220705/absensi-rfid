<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiswaAccount;
use Illuminate\Http\Request;
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
        $account = $request->user(); // karena SiswaAccount extends Authenticatable + HasApiTokens

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
}
