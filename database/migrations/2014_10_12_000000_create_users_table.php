<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'pengurus', 'anggota'])->default('anggota');
            $table->enum('account_status', ['pending', 'active', 'rejected', 'suspended'])->default('pending');
            $table->text('address')->nullable();
            $table->string('ktp_photo')->nullable();
            $table->string('selfie_photo')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
