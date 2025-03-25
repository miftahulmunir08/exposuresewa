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
        Schema::create('master_product', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->char('category_id', 36)->index();
            $table->foreign('category_id')->references('uuid')->on('master_product_category')->onDelete('cascade');
            $table->string('name');
            $table->string('kode_barang')->unique();
            $table->unsignedInteger('harga');
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['tersedia', 'dipinjam'])->default('tersedia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_product');
    }
};
