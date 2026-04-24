<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                                          // e.g. "Lapangan A", "Lapangan B"
            $table->enum('jenis', ['indoor', 'outdoor'])->default('indoor');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_per_jam', 10, 2);                        // Rp per jam
            $table->string('foto')->nullable();                              // path/URL foto lapangan
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapangan');
    }
};