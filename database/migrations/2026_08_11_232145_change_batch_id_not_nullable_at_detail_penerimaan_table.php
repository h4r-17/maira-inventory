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
        Schema::table('detail_penerimaan', function (Blueprint $table) {
            $table->foreignId('id_batch')->nullable(false)->change();
            $table->dropForeign(['id_barang']);
            $table->dropColumn('id_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_penerimaan', function (Blueprint $table) {
            $table->foreignId('id_batch')->nullable(true)->change();
            $table->foreignId('id_barang')->constrained('barang', 'id_barang')->onDelete('restrict');
        });
    }
};
