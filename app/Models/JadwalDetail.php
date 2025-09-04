<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDetail extends Model
{
    use HasFactory;

    protected $fillable = ['jadwal_id', 'hari', 'mapel', 'jam_mulai', 'jam_selesai'];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
