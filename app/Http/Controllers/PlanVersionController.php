<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\PlanVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PlanVersionController extends Controller
{
    /**
     * Display version history for a business plan.
     */
    public function index(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $versions = $businessPlan->planVersions()
            ->with('creator')
            ->latest()
            ->paginate(20);

        return view('business-plans.versions.index', compact('businessPlan', 'versions'));
    }

    /**
     * Create a new version snapshot.
     */
    public function store(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        $validated = $request->validate([
            'version_name' => 'nullable|string|max:255',
            'changes_summary' => 'nullable|string|max:1000',
        ]);

        // Get the latest version number
        $latestVersion = $businessPlan->planVersions()->latest()->first();
        $versionNumber = $latestVersion ? $latestVersion->version_number + 1 : 1;

        // Create snapshot of current state
        $snapshot = [
            'title' => $businessPlan->title,
            'description' => $businessPlan->description,
            'company_name' => $businessPlan->company_name,
            'vision' => $businessPlan->vision,
            'mission' => $businessPlan->mission,
            'status' => $businessPlan->status,
            'completion_percentage' => $businessPlan->completion_percentage,
            'chapters' => $businessPlan->chapters()->get()->map(function ($chapter) {
                return [
                    'title' => $chapter->title,
                    'content' => $chapter->content,
                    'sort_order' => $chapter->sort_order,
                    'status' => $chapter->status,
                ];
            })->toArray(),
        ];

        $version = $businessPlan->planVersions()->create([
            'version_number' => $versionNumber,
            'version_name' => $validated['version_name'] ?? "الإصدار {$versionNumber}",
            'snapshot' => $snapshot,
            'changes_summary' => $validated['changes_summary'],
            'created_by' => auth()->id(),
        ]);

        Log::info('Plan version created', [
            'plan_id' => $businessPlan->id,
            'version_id' => $version->id,
            'version_number' => $versionNumber,
        ]);

        return redirect()->back()->with('success', 'تم حفظ نسخة جديدة من الخطة بنجاح!');
    }

    /**
     * Show a specific version.
     */
    public function show(BusinessPlan $businessPlan, PlanVersion $version)
    {
        Gate::authorize('view', $businessPlan);

        if ($version->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        return view('business-plans.versions.show', compact('businessPlan', 'version'));
    }

    /**
     * Restore a specific version.
     */
    public function restore(Request $request, BusinessPlan $businessPlan, PlanVersion $version)
    {
        Gate::authorize('update', $businessPlan);

        if ($version->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        // Create a backup of current state before restoring
        $this->createBackupBeforeRestore($businessPlan);

        $snapshot = $version->snapshot;

        // Restore basic fields
        $businessPlan->update([
            'title' => $snapshot['title'] ?? $businessPlan->title,
            'description' => $snapshot['description'] ?? $businessPlan->description,
            'company_name' => $snapshot['company_name'] ?? $businessPlan->company_name,
            'vision' => $snapshot['vision'] ?? $businessPlan->vision,
            'mission' => $snapshot['mission'] ?? $businessPlan->mission,
        ]);

        // Restore chapters
        if (isset($snapshot['chapters']) && is_array($snapshot['chapters'])) {
            foreach ($snapshot['chapters'] as $chapterData) {
                $businessPlan->chapters()->updateOrCreate(
                    [
                        'sort_order' => $chapterData['sort_order'],
                    ],
                    [
                        'title' => $chapterData['title'],
                        'content' => $chapterData['content'],
                        'status' => $chapterData['status'],
                    ]
                );
            }
        }

        Log::info('Plan version restored', [
            'plan_id' => $businessPlan->id,
            'version_id' => $version->id,
            'version_number' => $version->version_number,
        ]);

        return redirect()->route('business-plans.show', $businessPlan)
            ->with('success', 'تم استعادة الإصدار بنجاح!');
    }

    /**
     * Compare two versions.
     */
    public function compare(BusinessPlan $businessPlan, PlanVersion $version1, PlanVersion $version2)
    {
        Gate::authorize('view', $businessPlan);

        if ($version1->business_plan_id !== $businessPlan->id || $version2->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        return view('business-plans.versions.compare', compact('businessPlan', 'version1', 'version2'));
    }

    /**
     * Delete a version.
     */
    public function destroy(BusinessPlan $businessPlan, PlanVersion $version)
    {
        Gate::authorize('update', $businessPlan);

        if ($version->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        $version->delete();

        Log::info('Plan version deleted', [
            'plan_id' => $businessPlan->id,
            'version_id' => $version->id,
        ]);

        return redirect()->back()->with('success', 'تم حذف الإصدار بنجاح!');
    }

    /**
     * Create a backup before restoring.
     */
    protected function createBackupBeforeRestore(BusinessPlan $businessPlan)
    {
        $latestVersion = $businessPlan->planVersions()->latest()->first();
        $versionNumber = $latestVersion ? $latestVersion->version_number + 1 : 1;

        $snapshot = [
            'title' => $businessPlan->title,
            'description' => $businessPlan->description,
            'company_name' => $businessPlan->company_name,
            'vision' => $businessPlan->vision,
            'mission' => $businessPlan->mission,
            'status' => $businessPlan->status,
            'completion_percentage' => $businessPlan->completion_percentage,
            'chapters' => $businessPlan->chapters()->get()->map(function ($chapter) {
                return [
                    'title' => $chapter->title,
                    'content' => $chapter->content,
                    'sort_order' => $chapter->sort_order,
                    'status' => $chapter->status,
                ];
            })->toArray(),
        ];

        $businessPlan->planVersions()->create([
            'version_number' => $versionNumber,
            'version_name' => "نسخة احتياطية قبل الاستعادة - {$versionNumber}",
            'snapshot' => $snapshot,
            'changes_summary' => 'نسخة احتياطية تلقائية قبل استعادة إصدار سابق',
            'created_by' => auth()->id(),
        ]);
    }
}
