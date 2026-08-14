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
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_retur')->nullable()->after('id_pembelian');
            $table->foreign('id_retur')->references('id_retur')->on('retur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->dropForeign(['id_retur']);
            $table->dropColumn('id_retur');
        });
    }
};
