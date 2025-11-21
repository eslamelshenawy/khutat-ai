<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Chapter extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chapters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'title',
        'slug',
        'content',
        'chapter_type',
        'sort_order',
        'status',
        'is_ai_generated',
        'ai_prompt',
        'ai_model_used',
        'ai_generated_at',
        'word_count',
        'locked_by',
        'locked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'sort_order' => 'integer',
        'word_count' => 'integer',
        'locked_by' => 'integer',
        'is_ai_generated' => 'boolean',
        'ai_generated_at' => 'datetime',
        'locked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_empty',
        'is_locked',
    ];

    /**
     * Get the business plan that owns the chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the user who locked this chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Get the AI generations for this chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aiGenerations()
    {
        return $this->hasMany(AiGeneration::class);
    }

    /**
     * Get the AI recommendations for this chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aiRecommendations()
    {
        return $this->hasMany(AiRecommendation::class);
    }

    /**
     * Check if the chapter is empty.
     *
     * @return bool
     */
    public function getIsEmptyAttribute()
    {
        return $this->status === 'empty' || empty($this->content);
    }

    /**
     * Check if the chapter is locked.
     *
     * @return bool
     */
    public function getIsLockedAttribute()
    {
        return $this->locked_by !== null && $this->locked_at !== null;
    }

    /**
     * Set the content and automatically calculate word count.
     *
     * @param string $value
     * @return void
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value;

        // Calculate word count (strip HTML tags first)
        $text = strip_tags($value);
        $this->attributes['word_count'] = str_word_count($text);
    }

    /**
     * Scope a query to only include chapters by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('chapter_type', $type);
    }

    /**
     * Scope a query to only include AI-generated chapters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    /**
     * Scope a query to only include completed chapters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->maxFileSize(5 * 1024 * 1024); // 5MB per image
    }
}
