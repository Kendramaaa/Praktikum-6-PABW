<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel laporan banjir yang dikirim warga.
     */
    public function up(): void
    {
        Schema::create('laporan_banjir', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('no_telepon', 20);
            $table->string('kecamatan');
            $table->string('lokasi');                       // jalan, RT/RW, desa
            $table->unsignedSmallInteger('tinggi_air');     // dalam sentimeter
            $table->text('keterangan')->nullable();
            $table->string('status')->default('baru');      // baru | diproses | selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_banjir');
    }
};
