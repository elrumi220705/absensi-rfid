<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            if (!Schema::hasColumn('peserta', 'jadwal_id')) {
                $table->foreignId('jadwal_id')
                    ->nullable()
                    ->constrained('jadwals')
                    ->nullOnDelete(); // set null jika jadwal dihapus
            }
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            if (Schema::hasColumn('peserta', 'jadwal_id')) {
                $table->dropConstrainedForeignId('jadwal_id');
            }
        });
    }
};
