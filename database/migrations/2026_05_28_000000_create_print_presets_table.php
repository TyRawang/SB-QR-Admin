<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_default')->default(false);

            $table->unsignedSmallInteger('cols')->default(4);
            $table->unsignedSmallInteger('rows')->default(5);
            $table->string('page_size', 16)->default('A4');
            $table->decimal('margin_top', 6, 2)->default(10);
            $table->decimal('margin_right', 6, 2)->default(10);
            $table->decimal('margin_bottom', 6, 2)->default(10);
            $table->decimal('margin_left', 6, 2)->default(10);
            $table->decimal('gap_x', 6, 2)->default(5);
            $table->decimal('gap_y', 6, 2)->default(5);
            $table->boolean('show_text')->default(true);
            $table->unsignedSmallInteger('text_size')->default(8);

            $table->string('logo_url', 2048)->nullable();
            $table->string('background_url', 2048)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_presets');
    }
};
