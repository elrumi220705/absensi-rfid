<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('id_rfid')->unique();
            $table->string('nama');

            // GANTI: pakai 'kelas', hilangkan 'asal_delegasi' & 'komisi'
            $table->string('kelas')->nullable();

            // Simpan singkat: 'L' / 'P'
            $table->enum('jenis_kelamin', ['L', 'P']);

            // Tambahkan tanggal daftar (dipakai di tampilan)
            $table->date('tanggal_daftar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
