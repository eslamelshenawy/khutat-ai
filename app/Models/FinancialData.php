<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialData extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_plan_id',
        'year',
        'revenue',
        'cost_of_goods_sold',
        'gross_profit',
        'operating_expenses',
        'operating_income',
        'net_income',
        'cash_inflow',
        'cash_outflow',
        'net_cash_flow',
        'assets',
        'liabilities',
        'equity',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
        'cost_of_goods_sold' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'operating_income' => 'decimal:2',
        'net_income' => 'decimal:2',
        'cash_inflow' => 'decimal:2',
        'cash_outflow' => 'decimal:2',
        'net_cash_flow' => 'decimal:2',
        'assets' => 'decimal:2',
        'liabilities' => 'decimal:2',
        'equity' => 'decimal:2',
    ];

    /**
     * Get the business plan that owns the financial data
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Calculate profit margin
     */
    public function getProfitMarginAttribute()
    {
        if ($this->revenue > 0) {
            return round(($this->net_income / $this->revenue) * 100, 2);
        }
        return 0;
    }

    /**
     * Calculate ROI
     */
    public function getRoiAttribute()
    {
        if ($this->equity > 0) {
            return round(($this->net_income / $this->equity) * 100, 2);
        }
        return 0;
    }
}
