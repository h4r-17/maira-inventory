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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->string('no_nota')->unique();
            $table->date('tanggal_pembelian');
            $table->foreignId('id_supplier')->constrained('supplier', 'id_supplier')->onDelete('restrict');
            $table->foreignId('id_pengajuan')->constrained('pengajuan', 'id_pengajuan')->onDelete('restrict');
            $table->enum('cara_bayar', ['Tunai', 'Net 14 Hari', 'Net 30 Hari']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
