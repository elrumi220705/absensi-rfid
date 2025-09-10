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
        'kelas',         // contoh: "X-IPA 1"
        'jenis_kelamin', // 'L'/'P'
        'tanggal_daftar',
        'foto',
        'jadwal_id',     // relasi ke jadwal (opsional)
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

    // Akun login siswa
    public function account()
    {
        return $this->hasOne(SiswaAccount::class, 'peserta_id');
    }

    // (opsional) alias agar kompatibel dengan kode lama
    public function akunSiswa()
    {
        return $this->account();
    }

    // Murid -> Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // ========= Helpers / Scopes =========
    public function scopeByKelas($query, ?string $kelas)
    {
        if ($kelas !== null && $kelas !== '') {
            $query->where('kelas', $kelas);
        }
        return $query;
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return match (strtoupper((string) $this->jenis_kelamin)) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }
}
