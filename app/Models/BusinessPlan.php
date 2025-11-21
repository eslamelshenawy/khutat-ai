<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BusinessPlan extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'slug',
        'description',
        'project_type',
        'industry_type',
        'status',
        'completion_percentage',
        'ai_score',
        'ai_feedback',
        'last_analyzed_at',
        'company_name',
        'company_logo',
        'vision',
        'mission',
        'language',
        'is_public',
        'allow_comments',
        'version',
        'parent_plan_id',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'template_id' => 'integer',
        'parent_plan_id' => 'integer',
        'completion_percentage' => 'integer',
        'ai_score' => 'integer',
        'version' => 'integer',
        'is_public' => 'boolean',
        'allow_comments' => 'boolean',
        'last_analyzed_at' => 'datetime',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_completed',
        'is_published',
    ];

    /**
     * Get the user that owns the business plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template associated with the business plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the parent plan (for versioning).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentPlan()
    {
        return $this->belongsTo(BusinessPlan::class, 'parent_plan_id');
    }

    /**
     * Get child versions of this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childVersions()
    {
        return $this->hasMany(BusinessPlan::class, 'parent_plan_id');
    }

    /**
     * Get the chapters for the business plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    /**
     * Get the business plan data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planData()
    {
        return $this->hasMany(BusinessPlanData::class);
    }

    /**
     * Get the AI generations for this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aiGenerations()
    {
        return $this->hasMany(AiGeneration::class);
    }

    /**
     * Get the shares for this business plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shares()
    {
        return $this->hasMany(BusinessPlanShare::class);
    }

    /**
     * Get active shares for this business plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeShares()
    {
        return $this->shares()->where('is_active', true)->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Get the chat messages for this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the financial simulations for this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function financialSimulations()
    {
        return $this->hasMany(FinancialSimulation::class);
    }

    /**
     * Get the plan versions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planVersions()
    {
        return $this->hasMany(PlanVersion::class);
    }

    /**
     * Get the AI recommendations for this plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aiRecommendations()
    {
        return $this->hasMany(AiRecommendation::class);
    }

    /**
     * Check if the plan is completed.
     *
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed' || $this->completion_percentage >= 100;
    }

    /**
     * Check if the plan is published.
     *
     * @return bool
     */
    public function getIsPublishedAttribute()
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Scope a query to only include plans by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include published plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include public plans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get the comments for this business plan
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('replies')->latest();
    }

    /**
     * Get the financial data for this business plan
     */
    public function financialData()
    {
        return $this->hasMany(FinancialData::class)->orderBy('year');
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->maxFileSize(5 * 1024 * 1024); // 5MB

        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->maxFileSize(5 * 1024 * 1024); // 5MB per image
    }
}
