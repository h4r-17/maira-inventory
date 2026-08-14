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
            $table->unsignedBigInteger('id_batch')->nullable()->change();
            $table->unsignedInteger('jumlah_ditolak')->nullable()->after('jumlah_masuk');
            $table->string('alasan_penolakan')->nullable()->after('jumlah_ditolak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_penerimaan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_ditolak', 'alasan_penolakan']);
            $table->unsignedBigInteger('id_batch')->nullable(false)->change();
        });
    }
};
