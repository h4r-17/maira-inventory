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
        Schema::create('detail_penerimaan', function (Blueprint $table) {
            $table->id('id_detail_penerimaan');
            $table->foreignId('id_penerimaan')->constrained('penerimaan', 'id_penerimaan')->onDelete('cascade');
            $table->foreignId('id_batch')->constrained('batch_barang', 'id_batch')->onDelete('restrict');
            $table->integer('jumlah_masuk');
            $table->integer('rasio_konversi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan');
    }
};
