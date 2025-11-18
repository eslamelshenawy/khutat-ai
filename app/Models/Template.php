<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'industry_type',
        'description',
        'structure',
        'ai_prompts',
        'custom_questions',
        'is_active',
        'is_featured',
        'sort_order',
        'thumbnail',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'structure' => 'array',
        'ai_prompts' => 'array',
        'custom_questions' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'chapters_count',
    ];

    /**
     * Get all business plans using this template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function businessPlans()
    {
        return $this->hasMany(BusinessPlan::class);
    }

    /**
     * Get the count of chapters in the template structure.
     *
     * @return int
     */
    public function getChaptersCountAttribute()
    {
        if (!is_array($this->structure) || !isset($this->structure['chapters'])) {
            return 0;
        }

        return count($this->structure['chapters']);
    }

    /**
     * Scope a query to only include active templates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured templates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by industry type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $industryType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIndustry($query, $industryType)
    {
        return $query->where('industry_type', $industryType);
    }
}
