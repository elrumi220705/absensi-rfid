<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'deskripsi',
        // GANTI: pakai kelas, bukan komisi
        'kelas',
        'jadwal_mulai',
        'jadwal_pulang',
    ];

    protected $casts = [
        'jadwal_mulai'  => 'string',
        'jadwal_pulang' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // (opsional) scope bantu
    public function scopeByKelas($q, ?string $kelas)
    {
        if ($kelas !== null && $kelas !== '') {
            $q->where('kelas', $kelas);
        }
        return $q;
    }
}
