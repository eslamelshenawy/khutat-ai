<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSimulation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'financial_simulations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'scenario_type',
        'data',
        'break_even_point',
        'roi_percentage',
        'total_revenue',
        'total_expenses',
        'net_profit',
        'chart_data',
        'is_ai_generated',
        'ai_model',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'data' => 'array',
        'chart_data' => 'array',
        'break_even_point' => 'integer',
        'roi_percentage' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'is_ai_generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profit_margin_percentage',
        'is_profitable',
    ];

    /**
     * Get the business plan that owns this simulation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Calculate profit margin percentage.
     *
     * @return float|null
     */
    public function getProfitMarginPercentageAttribute()
    {
        if (!$this->total_revenue || $this->total_revenue == 0) {
            return null;
        }

        return round(($this->net_profit / $this->total_revenue) * 100, 2);
    }

    /**
     * Check if the simulation shows profitability.
     *
     * @return bool
     */
    public function getIsProfitableAttribute()
    {
        return $this->net_profit > 0;
    }

    /**
     * Scope a query to filter by scenario type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByScenario($query, $type)
    {
        return $query->where('scenario_type', $type);
    }

    /**
     * Scope a query to only include profitable simulations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProfitable($query)
    {
        return $query->where('net_profit', '>', 0);
    }

    /**
     * Scope a query to only include AI-generated simulations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }
}
