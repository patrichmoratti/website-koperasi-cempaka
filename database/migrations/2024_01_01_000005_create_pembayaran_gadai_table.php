<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_gadai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_gadai_id')->constrained('transaksi_gadai')->onDelete('restrict');
            $table->enum('payment_type', ['bunga', 'tebus']);
            $table->string('month_covered', 7)->nullable();
            $table->json('paid_months')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('transfer_proof_path')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_gadai');
    }
};
