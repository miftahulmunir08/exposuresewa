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
        Schema::table('data_transaction', function (Blueprint $table) {
            $table->boolean('is_late')->nullable();
            $table->date('tanggal_pengembalian')->nullable();
            $table->string('biaya_denda')->nullable();
        });        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_transaction', function (Blueprint $table) {
            $table->dropColumn('is_late');
            $table->dropColumn('tanggal_pengembalian');
            $table->dropColumn('biaya_denda');
        });
    }
};
