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
        Schema::create('data_product_stock_log', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 50)->nullable();
            $table->char('product_id', 36)->index();
            $table->string('qty');
            $table->enum('status', ['baru', 'dipinjam', 'dikembalikan', 'hilang', 'rusak']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaction_code')->references('transaction_code')->on('data_transaction');
            $table->foreign('product_id')->references('uuid')->on('master_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_product_stock_log');
    }
};
