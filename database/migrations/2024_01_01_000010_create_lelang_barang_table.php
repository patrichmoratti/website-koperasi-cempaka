<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lelang_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_gadai_id')->constrained('transaksi_gadai')->onDelete('restrict');
            $table->date('auction_date');
            $table->decimal('auction_value', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lelang_barang');
    }
};
