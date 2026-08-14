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
        Schema::create('detail_produksi', function (Blueprint $table) {
            $table->id('id_detail_produksi');
            $table->foreignId('id_produksi')->constrained('produksi', 'id_produksi')->onDelete('cascade');
            $table->foreignId('id_batch')->constrained('batch_barang', 'id_batch')->onDelete('restrict');
            $table->integer('jumlah_keluar');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produksi');
    }
};
