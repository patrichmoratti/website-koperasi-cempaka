<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_gadai', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('jenis_barang_id');
            $table->string('item_video_path')->nullable()->after('item_photo_paths');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_gadai', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'item_video_path']);
        });
    }
};