<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_generations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'chapter_id',
        'user_id',
        'generation_type',
        'prompt',
        'response',
        'model_used',
        'tokens_used',
        'cost',
        'processing_time_ms',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'chapter_id' => 'integer',
        'user_id' => 'integer',
        'tokens_used' => 'integer',
        'processing_time_ms' => 'integer',
        'cost' => 'decimal:6',
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_successful',
        'processing_time_seconds',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Only created_at is used, no updated_at.
     *
     * @var bool
     */
    public const UPDATED_AT = null;

    /**
     * Get the business plan that owns this generation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the chapter that owns this generation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the user who requested this generation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the generation was successful.
     *
     * @return bool
     */
    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'completed' && empty($this->error_message);
    }

    /**
     * Get processing time in seconds.
     *
     * @return float|null
     */
    public function getProcessingTimeSecondsAttribute()
    {
        return $this->processing_time_ms ? round($this->processing_time_ms / 1000, 2) : null;
    }

    /**
     * Scope a query to only include successful generations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed')
            ->whereNull('error_message');
    }

    /**
     * Scope a query to only include failed generations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to filter by generation type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('generation_type', $type);
    }

    /**
     * Scope a query to filter by model used.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model_used', $model);
    }
}
