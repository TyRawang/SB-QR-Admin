<?php

namespace Database\Seeders;

use App\Models\PrintPreset;
use Illuminate\Database\Seeder;

class PrintPresetSeeder extends Seeder
{
    public function run(): void
    {
        if (PrintPreset::count() > 0) {
            return;
        }

        PrintPreset::create([
            'name' => 'Default',
            'is_default' => true,
            'cols' => 4,
            'rows' => 5,
            'page_size' => 'A4',
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'gap_x' => 5,
            'gap_y' => 5,
            'show_text' => true,
            'text_size' => 8,
            'logo_url' => null,
            'background_url' => null,
        ]);
    }
}
