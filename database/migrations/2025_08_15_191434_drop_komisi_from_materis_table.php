<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('materis', 'komisi')) {
            Schema::table('materis', function (Blueprint $table) {
                $table->dropColumn('komisi');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan kolom jika mau rollback (pakai string bebas, bukan ENUM lagi)
        Schema::table('materis', function (Blueprint $table) {
            if (!Schema::hasColumn('materis', 'komisi')) {
                $table->string('komisi', 100)->nullable()->after('deskripsi');
            }
        });
    }
};
