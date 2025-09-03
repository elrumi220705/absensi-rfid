<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            // kolom baru
            if (!Schema::hasColumn('peserta', 'kelas')) {
                $table->string('kelas', 50)->after('nama');
            }
            if (!Schema::hasColumn('peserta', 'tanggal_daftar')) {
                $table->date('tanggal_daftar')->nullable()->after('jenis_kelamin');
            }

            // jika masih ada kolom lama, buat nullable agar tidak ganggu
            if (Schema::hasColumn('peserta', 'asal_delegasi')) {
                $table->string('asal_delegasi')->nullable()->change();
            }
            if (Schema::hasColumn('peserta', 'komisi')) {
                // kalau kolom 'komisi' bertipe ENUM, ubah dulu jadi VARCHAR biar bisa di-null-kan
                // untuk MySQL: ganti ke string nullable
                $table->string('komisi')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            if (Schema::hasColumn('peserta', 'kelas')) {
                $table->dropColumn('kelas');
            }
            if (Schema::hasColumn('peserta', 'tanggal_daftar')) {
                $table->dropColumn('tanggal_daftar');
            }

            // optional: balikin kolom lama jadi NOT NULL sesuai skema lama kamu
            // (biasanya tidak perlu diisi, tergantung kebutuhan rollback)
        });
    }
};
