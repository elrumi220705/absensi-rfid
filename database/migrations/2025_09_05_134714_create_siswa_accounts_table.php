<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->unique()->constrained('peserta')->cascadeOnDelete();
            $table->string('login_id')->unique();   // default: id_rfid
            $table->string('password');             // hashed
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_accounts');
    }
};
