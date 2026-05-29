<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_presets', function (Blueprint $table) {
            $table->decimal('custom_width_mm', 7, 2)->nullable()->after('page_size');
            $table->decimal('custom_height_mm', 7, 2)->nullable()->after('custom_width_mm');
        });
    }

    public function down(): void
    {
        Schema::table('print_presets', function (Blueprint $table) {
            $table->dropColumn(['custom_width_mm', 'custom_height_mm']);
        });
    }
};
