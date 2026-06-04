<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_barang_gadai', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('base_value_per_unit', 15, 2)->default(0);
            $table->string('unit')->default('unit');
            $table->decimal('max_loan_percentage', 5, 2)->default(80);
            $table->string('image_path')->nullable();
            $table->json('conditions')->nullable();
            $table->json('brands')->nullable();
            $table->json('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_barang_gadai');
    }
};
