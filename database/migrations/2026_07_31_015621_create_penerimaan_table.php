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
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->id('id_penerimaan');
            $table->string('no_registrasi')->unique();
            $table->date('tanggal_masuk');
            $table->enum('jenis_penerimaan', ['Pembelian', 'Retur']);
            $table->foreignId('id_pembelian')->constrained('pembelian', 'id_pembelian')->onDelete('restrict');
            $table->string('surat_jalan')->nullable();
            $table->string('no_faktur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan');
    }
};
