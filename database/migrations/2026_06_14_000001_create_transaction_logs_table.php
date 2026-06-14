<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', [
                'pengajuan_gadai',
                'approval_gadai',
                'pembayaran_gadai',
                'pelunasan_gadai',
                'pembayaran_simpanan',
                'approval_simpanan',
                'registrasi',
            ]);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_type');
            $table->foreignId('anggota_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pengurus_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('status', ['committed', 'rolled_back']);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['transaction_type', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};