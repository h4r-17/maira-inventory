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
        Schema::table('detail_retur', function (Blueprint $table) {
            $table->integer('nilai_konversi')->after('jumlah_retur');
            $table->unsignedBigInteger('id_batch')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_retur', function (Blueprint $table) {
            $table->dropColumn('nilai_konversi');
            $table->unsignedBigInteger('id_batch')->nullable(false)->change();
        });
    }
};
