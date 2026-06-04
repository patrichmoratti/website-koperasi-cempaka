<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_operasional', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_operasional');
    }
};
