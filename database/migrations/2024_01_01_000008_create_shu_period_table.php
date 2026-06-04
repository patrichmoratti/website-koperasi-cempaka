<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_period', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year')->unique();
            $table->enum('status', ['open', 'closed', 'published'])->default('open');
            $table->decimal('total_income', 18, 2)->default(0);
            $table->decimal('total_expenses', 18, 2)->default(0);
            $table->decimal('total_shu', 18, 2)->default(0);
            $table->decimal('pct_dana_cadangan', 5, 2)->default(25);
            $table->decimal('pct_jasa_modal', 5, 2)->default(25);
            $table->decimal('pct_jasa_usaha', 5, 2)->default(30);
            $table->decimal('pct_dana_pengurus', 5, 2)->default(10);
            $table->decimal('pct_dana_pendidikan', 5, 2)->default(5);
            $table->decimal('pct_dana_sosial', 5, 2)->default(5);
            $table->decimal('alloc_dana_cadangan', 18, 2)->default(0);
            $table->decimal('alloc_jasa_modal', 18, 2)->default(0);
            $table->decimal('alloc_jasa_usaha', 18, 2)->default(0);
            $table->decimal('alloc_dana_pengurus', 18, 2)->default(0);
            $table->decimal('alloc_dana_pendidikan', 18, 2)->default(0);
            $table->decimal('alloc_dana_sosial', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_period');
    }
};
