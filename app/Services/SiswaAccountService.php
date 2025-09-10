<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\SiswaAccount;
use Illuminate\Support\Str;

class SiswaAccountService
{
    public static function ensureForPeserta(Peserta $p, ?string $plainPassword = null): SiswaAccount
    {
        $acc = $p->account ?: new SiswaAccount(['peserta_id' => $p->id]);

        // login_id default = id_rfid
        $acc->login_id = $acc->login_id ?: $p->id_rfid;

        // password default (acak / custom)
        if (!$acc->exists || $plainPassword) {
            $acc->setPlainPassword($plainPassword ?: Str::random(8));
        }

        $acc->is_active = true;
        $acc->save();

        return $acc;
    }
}
