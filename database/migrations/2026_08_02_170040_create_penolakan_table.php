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
        Schema::create('penolakan', function (Blueprint $table) {
            $table->id('id_penolakan');
            $table->string('no_penolakan')->unique();
            $table->date('tanggal_penolakan');
            $table->foreignId('id_produksi')->constrained('produksi', 'id_produksi')->onDelete('restrict');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penolakan');
    }
};
