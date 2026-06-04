<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_gadai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('pengajuan_id')->constrained('pengajuan_gadai')->onDelete('restrict');
            $table->foreignId('jenis_barang_id')->constrained('jenis_barang_gadai')->onDelete('restrict');
            $table->text('item_description');
            $table->json('item_photo_paths')->nullable();
            $table->decimal('appraisal_value', 15, 2)->default(0);
            $table->decimal('loan_amount', 15, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(8.00);
            $table->date('pawn_date');
            $table->date('due_date');
            $table->enum('status', ['diproses', 'verifikasi', 'aktif', 'selesai', 'menunggu_lelang', 'dilelang', 'ditebus'])->default('diproses');
            $table->string('warehouse_location')->nullable();
            $table->string('reference_number')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_gadai');
    }
};
