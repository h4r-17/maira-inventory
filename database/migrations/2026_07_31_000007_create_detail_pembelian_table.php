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
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id('id_detail_pembelian');
            $table->foreignId('id_pembelian')->constrained('pembelian', 'id_pembelian')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
            $table->text('deskripsi')->nullable();
            $table->integer('kuantitas');
            $table->foreignId('id_satuan')->constrained('satuan', 'id_satuan')->onDelete('restrict');
            $table->decimal('harga', 15, 2);
            $table->decimal('pajak', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['id_pembelian', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
