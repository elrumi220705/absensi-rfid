<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'peserta_id',
        'materi_id',
        'status',
        'keterangan',
        'waktu_absen',
        'waktu_datang',
        'waktu_pulang',   // <— baru
    ];

    protected $casts = [
        'waktu_absen'  => 'datetime',
        'waktu_datang' => 'datetime',
        'waktu_pulang' => 'datetime',   // <— baru
    ];

    public function peserta() { return $this->belongsTo(Peserta::class); }
    public function materi()  { return $this->belongsTo(Materi::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public static function statusOptions()
    {
        return [
            'hadir'        => 'Hadir',
            'terlambat'    => 'Terlambat',
            'tidak_hadir'  => 'Tidak Hadir',
        ];
    }
}

