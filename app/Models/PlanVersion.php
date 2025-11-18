<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanVersion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'version_number',
        'version_name',
        'snapshot',
        'changes_summary',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'version_number' => 'integer',
        'created_by' => 'integer',
        'snapshot' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Only created_at is used, no updated_at.
     *
     * @var bool
     */
    public const UPDATED_AT = null;

    /**
     * Get the business plan that owns this version.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the user who created this version.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to filter by version number.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $versionNumber
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVersion($query, $versionNumber)
    {
        return $query->where('version_number', $versionNumber);
    }

    /**
     * Scope a query to get the latest version.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('version_number', 'desc');
    }

    /**
     * Scope a query to get versions created by a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
