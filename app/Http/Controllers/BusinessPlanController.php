<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BusinessPlanController extends Controller
{
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

        return redirect()->route('wizard.steps', ['businessPlan' => $businessPlan->id]);
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

        $exportService = new ExportService();

        try {
            $path = match($format) {
                'pdf' => $exportService->exportToPDF($businessPlan),
                'word', 'docx' => $exportService->exportToWord($businessPlan),
                'excel', 'xlsx' => $exportService->exportToExcel($businessPlan),
                default => null,
            };

            if (!$path) {
                return redirect()->back()->with('error', 'صيغة التصدير غير مدعومة');
            }

            return Storage::download($path);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التصدير: ' . $e->getMessage());
        }
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
