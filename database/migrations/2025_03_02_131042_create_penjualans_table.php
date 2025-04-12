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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_penjualan');
            $table->string(('nama_pembeli'));
            $table->integer('total_harga_akhir');
            $table->integer('total_item');
            $table->integer('total_uang')->nullable();
            $table->integer('kembalian')->nullable();
            $table->string('metode');
            $table->string('status');
            $table->string('no_faktur');
            $table->string('kode_cek')->nullable();
            $table->date('tanggal_cair')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
