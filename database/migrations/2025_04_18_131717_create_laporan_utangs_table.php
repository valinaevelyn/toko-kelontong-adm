<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_utangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->references('id')->on('pembelians')->onDelete('cascade')->onUpdate('cascade');
            $table->date('tanggal');
            $table->string('nama');
            $table->string('keterangan');
            $table->integer('jumlah_utang');
            $table->date('jatuh_tempo');
            $table->integer('status_terlambat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_utangs');
    }
};
