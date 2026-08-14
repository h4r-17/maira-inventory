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
        Schema::create('produksi', function (Blueprint $table) {
            $table->id('id_produksi');
            $table->string('batch_produk');
            $table->date('tanggal_produksi');
            $table->foreignId('id_produk')->constrained('barang_jadi', 'id_produk')->onDelete('restrict');
            $table->integer('hasil_produksi');
            $table->date('produk_expired');
            $table->string('tujuan_produksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
