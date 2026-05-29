<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'is_default',
    'cols', 'rows', 'page_size',
    'margin_top', 'margin_right', 'margin_bottom', 'margin_left',
    'gap_x', 'gap_y',
    'show_text', 'text_size',
    'logo_url', 'background_url',
])]
class PrintPreset extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'cols' => 'integer',
            'rows' => 'integer',
            'margin_top' => 'float',
            'margin_right' => 'float',
            'margin_bottom' => 'float',
            'margin_left' => 'float',
            'gap_x' => 'float',
            'gap_y' => 'float',
            'show_text' => 'boolean',
            'text_size' => 'integer',
        ];
    }

    public const PAGE_SIZES = ['A4', 'A5', 'Letter', 'Legal'];

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public static function defaultPreset(): ?self
    {
        return static::default()->first() ?? static::orderBy('id')->first();
    }

    public function makeDefault(): void
    {
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    public function toEdgeFunctionPayload(): array
    {
        return [
            'layout' => [
                'cols' => $this->cols,
                'rows' => $this->rows,
                'page_size' => $this->page_size,
                'margins' => [
                    'top' => $this->margin_top,
                    'right' => $this->margin_right,
                    'bottom' => $this->margin_bottom,
                    'left' => $this->margin_left,
                ],
                'gaps' => [
                    'x' => $this->gap_x,
                    'y' => $this->gap_y,
                ],
                'show_text' => $this->show_text,
                'text_size' => $this->text_size,
            ],
            'logo_url' => $this->logo_url,
            'background_url' => $this->background_url,
        ];
    }
}
