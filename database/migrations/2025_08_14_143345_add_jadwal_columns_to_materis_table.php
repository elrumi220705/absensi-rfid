<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            // Tambah kolom hanya jika belum ada (aman untuk rerun)
            if (!Schema::hasColumn('materis', 'jadwal_mulai')) {
                $table->time('jadwal_mulai')->nullable()->after('komisi');
            }
            if (!Schema::hasColumn('materis', 'jadwal_pulang')) {
                $table->time('jadwal_pulang')->nullable()->after('jadwal_mulai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            if (Schema::hasColumn('materis', 'jadwal_pulang')) {
                $table->dropColumn('jadwal_pulang');
            }
            if (Schema::hasColumn('materis', 'jadwal_mulai')) {
                $table->dropColumn('jadwal_mulai');
            }
        });
    }
};
