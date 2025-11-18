<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BusinessPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $businessPlans = auth()->user()->businessPlans()
            ->withCount('chapters')
            ->latest()
            ->paginate(12);

        return view('business-plans.index', compact('businessPlans'));
    }

    public function show(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $businessPlan->load(['chapters', 'template', 'aiRecommendations']);

        return view('business-plans.show', compact('businessPlan'));
    }

    public function edit(BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        return redirect()->route('wizard.steps', $businessPlan);
    }

    public function destroy(BusinessPlan $businessPlan)
    {
        Gate::authorize('delete', $businessPlan);

        $businessPlan->delete();

        return redirect()->route('business-plans.index')
            ->with('success', 'تم حذف خطة العمل بنجاح');
    }

    public function export(BusinessPlan $businessPlan, $format = 'pdf')
    {
        Gate::authorize('view', $businessPlan);

        $businessPlan->load('chapters');

        if ($format === 'pdf') {
            return $this->exportPdf($businessPlan);
        } elseif ($format === 'docx') {
            return $this->exportDocx($businessPlan);
        }

        return redirect()->back()->with('error', 'صيغة التصدير غير مدعومة');
    }

    protected function exportPdf(BusinessPlan $businessPlan)
    {
        // TODO: Implement PDF export using DomPDF or similar
        return response()->json(['message' => 'PDF export coming soon']);
    }

    protected function exportDocx(BusinessPlan $businessPlan)
    {
        // TODO: Implement DOCX export using PHPWord or similar
        return response()->json(['message' => 'DOCX export coming soon']);
    }

    public function duplicate(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $newPlan = $businessPlan->replicate();
        $newPlan->title = $businessPlan->title . ' (نسخة)';
        $newPlan->slug = $businessPlan->slug . '-copy-' . time();
        $newPlan->status = 'draft';
        $newPlan->is_public = false;
        $newPlan->published_at = null;
        $newPlan->save();

        // Duplicate chapters
        foreach ($businessPlan->chapters as $chapter) {
            $newChapter = $chapter->replicate();
            $newChapter->business_plan_id = $newPlan->id;
            $newChapter->save();
        }

        return redirect()->route('business-plans.show', $newPlan)
            ->with('success', 'تم نسخ خطة العمل بنجاح');
    }
}
