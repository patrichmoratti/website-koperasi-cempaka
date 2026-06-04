<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_distribution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shu_period_id')->constrained('shu_period')->onDelete('cascade');
            $table->foreignId('anggota_id')->constrained('users')->onDelete('restrict');
            $table->decimal('total_savings', 18, 2)->default(0);
            $table->decimal('member_savings_proportion', 10, 8)->default(0);
            $table->decimal('member_jasa_modal', 15, 2)->default(0);
            $table->decimal('total_interest_paid', 18, 2)->default(0);
            $table->decimal('member_interest_proportion', 10, 8)->default(0);
            $table->decimal('member_jasa_usaha', 15, 2)->default(0);
            $table->decimal('total_shu_received', 15, 2)->default(0);
            $table->enum('withdrawal_status', ['pending', 'withdrawn'])->default('pending');
            $table->timestamp('withdrawal_date')->nullable();
            $table->timestamps();
            $table->unique(['shu_period_id', 'anggota_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_distribution');
    }
};
