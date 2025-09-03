<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            if (!Schema::hasColumn('peserta', 'kelas')) {
                $table->string('kelas', 50)->nullable()->after('nama');
            }
            if (!Schema::hasColumn('peserta', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L','P'])->nullable()->after('kelas');
            }
            if (!Schema::hasColumn('peserta', 'tanggal_daftar')) {
                $table->date('tanggal_daftar')->nullable()->after('jenis_kelamin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            if (Schema::hasColumn('peserta', 'tanggal_daftar')) $table->dropColumn('tanggal_daftar');
            if (Schema::hasColumn('peserta', 'jenis_kelamin'))  $table->dropColumn('jenis_kelamin');
            if (Schema::hasColumn('peserta', 'kelas'))          $table->dropColumn('kelas');
        });
    }
};
