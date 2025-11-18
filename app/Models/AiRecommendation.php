<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRecommendation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_recommendations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'chapter_id',
        'recommendation_type',
        'priority',
        'title',
        'description',
        'suggested_action',
        'status',
        'applied_by',
        'applied_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'chapter_id' => 'integer',
        'applied_by' => 'integer',
        'applied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_pending',
        'is_applied',
        'is_critical',
    ];

    /**
     * Get the business plan that owns this recommendation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the chapter that owns this recommendation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the user who applied this recommendation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appliedByUser()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Check if the recommendation is pending.
     *
     * @return bool
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the recommendation has been applied.
     *
     * @return bool
     */
    public function getIsAppliedAttribute()
    {
        return $this->status === 'applied';
    }

    /**
     * Check if the recommendation is critical priority.
     *
     * @return bool
     */
    public function getIsCriticalAttribute()
    {
        return $this->priority === 'critical';
    }

    /**
     * Scope a query to only include pending recommendations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include applied recommendations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    /**
     * Scope a query to filter by priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include critical recommendations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Scope a query to filter by recommendation type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('recommendation_type', $type);
    }

    /**
     * Scope a query to order by priority (critical first).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')");
    }
}
