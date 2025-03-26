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
        Schema::create('data_cart', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('user_id')->index();
            $table->foreign('user_id')->references('uuid')->on('users');
            $table->string('transaction_code');
            $table->foreign('transaction_code')->references('transaction_code')->on('data_transaction');
            $table->char('product_id', 36)->index();
            $table->foreign('product_id')->references('uuid')->on('master_product');
            $table->string('qty');
            $table->string('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_cart');
    }
};
