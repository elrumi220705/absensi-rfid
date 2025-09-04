<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = ['nama_jadwal'];

    public function details()
    {
        return $this->hasMany(JadwalDetail::class);
    }

    // Opsional: relasi ke Peserta (kalau perlu melihat semua murid yang pakai jadwal ini)
    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }
}
