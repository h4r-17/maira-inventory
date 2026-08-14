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
        Schema::table('retur', function (Blueprint $table) {
            $table->foreignId('id_penerimaan')->nullable()->after('id_pembelian')->constrained('penerimaan', 'id_penerimaan')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur', function (Blueprint $table) {
            $table->dropForeign(['id_penerimaan']);
            $table->dropColumn('id_penerimaan');
        });
    }
};
