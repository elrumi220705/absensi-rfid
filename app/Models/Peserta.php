<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'user_id',
        'id_rfid',
        'nama',
        'kelas',            // contoh: "X-IPA 1"
        'jenis_kelamin',    // 'L' / 'P'
        'tanggal_daftar',   // DATE
        'foto',             // path foto profil
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
    ];

    // ========= Relasi =========
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'peserta_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========= Helpers / Scopes =========
    public function scopeByKelas($query, ?string $kelas)
    {
        if ($kelas !== null && $kelas !== '') {
            $query->where('kelas', $kelas);
        }
        return $query;
    }

    // Label ramah UI dari kode 'L'/'P'
    public function getJenisKelaminLabelAttribute(): string
    {
        return match (strtoupper((string) $this->jenis_kelamin)) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    // URL Foto Profil (fallback default kalau null)
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }
}
