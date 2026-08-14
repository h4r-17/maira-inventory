<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resep_produksi', function (Blueprint $table) {
            $table->id('id_resep');
            $table->foreignId('id_produk')->constrained('barang_jadi', 'id_produk')->onDelete('restrict');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
            $table->integer('standar_kuantitas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_produksi');
    }
};
