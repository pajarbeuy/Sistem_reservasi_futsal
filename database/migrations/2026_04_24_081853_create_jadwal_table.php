<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapangan_id')
                  ->constrained('lapangan')
                  ->onDelete('cascade');   // hapus jadwal jika lapangan dihapus
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['tersedia', 'dipesan', 'ditutup'])
                  ->default('tersedia');

            // Mencegah slot duplikat pada lapangan + tanggal + jam yang sama
            $table->unique(['lapangan_id', 'tanggal', 'jam_mulai'], 'unique_slot');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};