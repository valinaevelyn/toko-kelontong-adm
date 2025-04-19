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
        Schema::create('laporan_items', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('jumlah_pembelian')->default(0);
            $table->integer('jumlah_penjualan')->default(0);
            $table->integer('sisa_stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_items');
    }
};
