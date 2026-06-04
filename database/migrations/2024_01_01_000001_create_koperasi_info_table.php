<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasi_info', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Koperasi Cempaka');
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number', 30)->nullable();
            $table->string('bank_account_name')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasi_info');
    }
};
