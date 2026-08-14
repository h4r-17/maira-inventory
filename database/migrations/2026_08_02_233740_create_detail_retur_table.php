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
        Schema::create('detail_retur', function (Blueprint $table) {
            $table->id('id_detail_retur');
            $table->foreignId('id_retur')->constrained('retur', 'id_retur')->onDelete('cascade');
            $table->foreignId('id_batch')->constrained('batch_barang', 'id_batch')->onDelete('restrict');
            $table->foreignId('id_satuan')->constrained('satuan', 'id_satuan')->onDelete('restrict');
            $table->integer('jumlah_retur');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_retur');
    }
};
