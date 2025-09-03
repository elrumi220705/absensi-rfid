<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Absensi
    |--------------------------------------------------------------------------
    |
    | grace_minutes menentukan toleransi keterlambatan (dalam menit) untuk
    | penilaian status "hadir" vs "terlambat". Nilai default diambil dari
    | environment variable ABSEN_GRACE_MINUTES, fallback ke 10 menit.
    |
    */

    'grace_minutes' => (int) env('ABSEN_GRACE_MINUTES', 3),

];
