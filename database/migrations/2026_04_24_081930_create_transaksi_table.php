<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                  ->constrained('booking')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('kode_transaksi')->unique();   // e.g. TRX-20260424-0001
            $table->decimal('jumlah', 12, 2);             // total yang dibayar
            $table->enum('metode', [
                'transfer',   // transfer bank
                'cash',       // bayar langsung di tempat
                'qris',       // QRIS / e-wallet
            ])->default('transfer');
            $table->string('bukti_pembayaran')->nullable(); // path upload bukti transfer
            $table->enum('status', [
                'pending',   // menunggu konfirmasi admin
                'sukses',    // pembayaran diterima
                'gagal',     // pembayaran gagal / expire
                'refund',    // dana dikembalikan
            ])->default('pending');
            $table->timestamp('tanggal_bayar')->nullable(); // waktu konfirmasi sukses
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};