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
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->string('no_retur')->unique();
            $table->date('tanggal_retur');
            $table->foreignId('id_penolakan')->nullable()->constrained('penolakan', 'id_penolakan')->onDelete('restrict');
            $table->foreignId('id_pembelian')->constrained('pembelian', 'id_pembelian')->onDelete('restrict');
            $table->enum('status', ['Diretur', 'Pending', 'Ditolak'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur');
    }
};
