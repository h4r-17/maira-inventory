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
        Schema::create('detail_pengajuan', function (Blueprint $table) {
            $table->id('id_detail_pengajuan');
            $table->foreignId('id_pengajuan')->constrained('pengajuan', 'id_pengajuan')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
            $table->foreignId('id_satuan')->constrained('satuan', 'id_satuan')->onDelete('restrict');
            $table->integer('kuantitas');
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['id_pengajuan', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengajuan');
    }
};
