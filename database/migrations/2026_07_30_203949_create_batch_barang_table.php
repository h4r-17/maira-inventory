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
        Schema::create('batch_barang', function (Blueprint $table) {
            $table->id('id_batch');
            $table->string('kode_batch')->unique();
            $table->string('kode_lot_supplier')->nullable();
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
            $table->date('expired_date')->nullable();
            $table->integer('sisa_persediaan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_barang');
    }
};
