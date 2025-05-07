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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('merek');
            $table->string('kategori');
            // $table->string('uom');
            $table->integer('harga_jual');
            // $table->integer('harga_beli');
            // $table->integer('stock');

            $table->integer('stock_dus')->nullable()->default(0);
            $table->integer('stock_rcg')->nullable()->default(0);
            $table->integer('stock_pcs')->nullable()->default(0);

            $table->integer('dus_in_pcs')->nullable()->default(0);
            $table->integer('rcg_in_pcs')->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
