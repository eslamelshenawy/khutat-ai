<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'font_size_base',
        'include_header',
        'include_footer',
        'include_page_numbers',
        'include_table_of_contents',
        'header_text',
        'footer_text',
        'company_name',
        'website',
        'email',
        'phone',
        'template_type',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'include_header' => 'boolean',
        'include_footer' => 'boolean',
        'include_page_numbers' => 'boolean',
        'include_table_of_contents' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'font_size_base' => 'integer',
    ];

    /**
     * Get the user that owns the template
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get primary color as RGB array
     */
    public function getPrimaryColorRgb(): array
    {
        return $this->hexToRgb($this->primary_color);
    }

    /**
     * Get secondary color as RGB array
     */
    public function getSecondaryColorRgb(): array
    {
        return $this->hexToRgb($this->secondary_color);
    }

    /**
     * Get accent color as RGB array
     */
    public function getAccentColorRgb(): array
    {
        return $this->hexToRgb($this->accent_color);
    }

    /**
     * Convert hex color to RGB array
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Scope query to only include active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to only include templates for a specific type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('template_type', $type)
              ->orWhere('template_type', 'all');
        });
    }
}
