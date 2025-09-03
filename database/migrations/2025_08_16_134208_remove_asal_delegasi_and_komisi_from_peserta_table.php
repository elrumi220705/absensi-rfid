<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel ada dulu
        if (!Schema::hasTable('peserta')) {
            return;
        }

        // Kumpulkan kolom yang memang masih ada
        $candidates = ['asal_delegasi', 'komisi'];
        $toDrop = array_values(array_filter($candidates, fn ($col) => Schema::hasColumn('peserta', $col)));

        // Hanya drop kalau ada yang perlu di-drop
        if (!empty($toDrop)) {
            Schema::table('peserta', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('peserta')) {
            return;
        }

        Schema::table('peserta', function (Blueprint $table) {
            if (!Schema::hasColumn('peserta', 'asal_delegasi')) {
                $table->string('asal_delegasi')->nullable();
            }
            if (!Schema::hasColumn('peserta', 'komisi')) {
                $table->string('komisi')->nullable();
            }
        });
    }
};
