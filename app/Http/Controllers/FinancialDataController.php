<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\FinancialData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinancialDataController extends Controller
{
    /**
     * Show financial data page
     */
    public function index(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $financialData = $businessPlan->financialData;

        return view('business-plans.financial.index', compact('businessPlan', 'financialData'));
    }

    /**
     * Store or update financial data for a year
     */
    public function store(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2050',
            'revenue' => 'required|numeric|min:0',
            'cost_of_goods_sold' => 'required|numeric|min:0',
            'operating_expenses' => 'required|numeric|min:0',
            'cash_inflow' => 'nullable|numeric|min:0',
            'cash_outflow' => 'nullable|numeric|min:0',
            'assets' => 'nullable|numeric|min:0',
            'liabilities' => 'nullable|numeric|min:0',
        ]);

        // Calculate derived values
        $validated['gross_profit'] = $validated['revenue'] - $validated['cost_of_goods_sold'];
        $validated['operating_income'] = $validated['gross_profit'] - $validated['operating_expenses'];
        $validated['net_income'] = $validated['operating_income']; // Simplified
        $validated['net_cash_flow'] = ($validated['cash_inflow'] ?? 0) - ($validated['cash_outflow'] ?? 0);
        $validated['equity'] = ($validated['assets'] ?? 0) - ($validated['liabilities'] ?? 0);

        $businessPlan->financialData()->updateOrCreate(
            ['year' => $validated['year']],
            $validated
        );

        return back()->with('success', 'تم حفظ البيانات المالية بنجاح');
    }

    /**
     * Delete financial data for a year
     */
    public function destroy(BusinessPlan $businessPlan, FinancialData $financialData)
    {
        Gate::authorize('update', $businessPlan);

        $financialData->delete();

        return back()->with('success', 'تم حذف البيانات المالية');
    }
}
