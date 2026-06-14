<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pengajuan_gadai MODIFY COLUMN status ENUM('proses', 'diterima', 'ditolak', 'dibatalkan') NOT NULL DEFAULT 'proses'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengajuan_gadai MODIFY COLUMN status ENUM('proses', 'diterima', 'ditolak') NOT NULL DEFAULT 'proses'");
    }
};
