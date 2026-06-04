<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['pokok', 'wajib']);
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('period_month')->nullable();
            $table->smallInteger('period_year')->nullable();
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
        Schema::dropIfExists('simpanan');
    }
};
