<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_gadai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('jenis_barang_id')->constrained('jenis_barang_gadai')->onDelete('restrict');
            $table->text('description');
            $table->string('weight_or_quantity')->nullable();
            $table->string('condition');
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->decimal('loan_request_amount', 15, 2)->default(0);
            $table->json('item_photo_paths')->nullable();
            $table->json('supporting_doc_paths')->nullable();
            $table->enum('status', ['proses', 'diterima', 'ditolak'])->default('proses');
            $table->text('reason_if_rejected')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_gadai');
    }
};
