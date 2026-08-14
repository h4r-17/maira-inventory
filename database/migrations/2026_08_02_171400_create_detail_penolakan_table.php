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
        Schema::create('detail_penolakan', function (Blueprint $table) {
            $table->id('id_detail_penolakan');
            $table->foreignId('id_penolakan')->constrained('penolakan', 'id_penolakan')->onDelete('cascade');
            $table->foreignId('id_batch')->constrained('batch_barang', 'id_batch')->onDelete('restrict');
            $table->integer('jumlah_ditolak');
            $table->string('alasan_penolakan');
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penolakan');
    }
};
