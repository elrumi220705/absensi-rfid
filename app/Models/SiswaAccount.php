<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // penting
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class SiswaAccount extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'siswa_accounts';

    protected $fillable = [
        'peserta_id',
        'login_id',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    // ===== Helpers =====
    public function setPlainPassword(string $plain): void
    {
        $this->password = Hash::make($plain);
    }

    public function checkPassword(string $plain): bool
    {
        return Hash::check($plain, $this->password);
    }
}
