<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('lapangan_id')
                  ->constrained('lapangan')
                  ->onDelete('cascade');
            $table->foreignId('jadwal_id')
                  ->constrained('jadwal')
                  ->onDelete('cascade');
            $table->string('kode_booking')->unique();   // e.g. BK-20260424-0001
            $table->unsignedInteger('durasi_jam');       // jumlah jam booking
            $table->decimal('total_harga', 12, 2);      // harga_per_jam * durasi_jam
            $table->enum('status', [
                'menunggu',     // booking dibuat, belum dibayar
                'dikonfirmasi', // pembayaran sudah diverifikasi admin
                'dibatalkan',   // dibatalkan user / admin
                'selesai',      // sesi selesai dimainkan
            ])->default('menunggu');
            $table->text('catatan')->nullable();        // catatan tambahan dari user
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};