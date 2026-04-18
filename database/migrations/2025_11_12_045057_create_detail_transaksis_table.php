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
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('transaksi_id')
                  ->constrained('transaksis')
                  ->onDelete('cascade');

            $table->foreignId('produk_id')
                  ->constrained('produks')
                  ->onDelete('cascade');

            $table->integer('jumlah')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
