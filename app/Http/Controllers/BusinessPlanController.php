<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use App\Services\ExportService;
use App\Services\RecommendationService;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BusinessPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->businessPlans()->withCount('chapters');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by project type
        if ($request->filled('project_type')) {
            $query->where('project_type', $request->input('project_type'));
        }

        // Filter by industry
        if ($request->filled('industry_type')) {
            $query->where('industry_type', $request->input('industry_type'));
        }

        // Filter by completion percentage range
        if ($request->filled('completion_min')) {
            $query->where('completion_percentage', '>=', $request->input('completion_min'));
        }
        if ($request->filled('completion_max')) {
            $query->where('completion_percentage', '<=', $request->input('completion_max'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSortFields = ['created_at', 'updated_at', 'title', 'completion_percentage', 'ai_score'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $businessPlans = $query->paginate(12)->withQueryString();

        // Get filter options for dropdown
        $statuses = ['draft', 'in_progress', 'review', 'completed', 'archived'];
        $projectTypes = auth()->user()->businessPlans()->select('project_type')->distinct()->pluck('project_type')->filter();
        $industryTypes = auth()->user()->businessPlans()->select('industry_type')->distinct()->pluck('industry_type')->filter();

        return view('business-plans.index', compact('businessPlans', 'statuses', 'projectTypes', 'industryTypes'));
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

    /**
     * Analyze business plan and generate AI score and feedback
     */
    public function analyze(BusinessPlan $businessPlan, OllamaService $ollamaService)
    {
        Gate::authorize('update', $businessPlan);

        try {
            $businessPlan->load(['chapters', 'template']);

            // Use OllamaService's analyzePlanQuality method
            $analysis = $ollamaService->analyzePlanQuality($businessPlan);

            // Update business plan with AI analysis
            $businessPlan->update([
                'ai_score' => $analysis['score'] ?? null,
                'ai_feedback' => $analysis['feedback'] ?? 'تم تحليل الخطة بنجاح',
            ]);

            return redirect()->back()->with('success', 'تم تحليل خطة العمل بنجاح');

        } catch (\Exception $e) {
            Log::error('Business Plan Analysis Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحليل خطة العمل: ' . $e->getMessage());
        }
    }

    /**
     * Generate recommendations for a business plan
     */
    public function recommendations(BusinessPlan $businessPlan, RecommendationService $recommendationService)
    {
        Gate::authorize('view', $businessPlan);

        try {
            $recommendations = $recommendationService->generateRecommendations($businessPlan);

            return redirect()->back()->with('success', 'تم توليد ' . count($recommendations) . ' توصية بنجاح');

        } catch (\Exception $e) {
            Log::error('Recommendations Generation Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء توليد التوصيات');
        }
    }

    /**
     * Parse AI analysis response
     */
    protected function parseAnalysisResponse(string $response): array
    {
        try {
            // Try to extract JSON from response
            if (preg_match('/\{.*\}/s', $response, $matches)) {
                $json = json_decode($matches[0], true);
                if ($json) {
                    return $json;
                }
            }

            // Fallback: parse manually
            $score = null;
            if (preg_match('/["\']?score["\']?\s*:\s*(\d+)/', $response, $matches)) {
                $score = (int) $matches[1];
            }

            return [
                'score' => $score ?? 70,
                'strengths' => $this->extractList($response, 'strengths|نقاط القوة|القوة'),
                'weaknesses' => $this->extractList($response, 'weaknesses|نقاط الضعف|الضعف'),
                'recommendations' => $this->extractList($response, 'recommendations|توصيات|التوصيات'),
            ];

        } catch (\Exception $e) {
            Log::error('Parse Analysis Response Error', ['error' => $e->getMessage()]);
            return [
                'score' => 70,
                'strengths' => ['خطة العمل شاملة'],
                'weaknesses' => ['يمكن تحسين بعض الأقسام'],
                'recommendations' => ['مراجعة المحتوى وإضافة تفاصيل أكثر'],
            ];
        }
    }

    /**
     * Extract list items from text
     */
    protected function extractList(string $text, string $pattern): array
    {
        $items = [];

        // Try to find JSON array
        if (preg_match('/"(?:' . $pattern . ')"\s*:\s*\[(.*?)\]/s', $text, $matches)) {
            preg_match_all('/"([^"]+)"/', $matches[1], $itemMatches);
            if (!empty($itemMatches[1])) {
                return array_slice($itemMatches[1], 0, 5);
            }
        }

        // Fallback: find numbered or bulleted lists
        preg_match_all('/(?:^|\n)\s*[\d\-\*•]\s*\.?\s*(.+?)(?=\n|$)/m', $text, $matches);
        if (!empty($matches[1])) {
            return array_slice(array_map('trim', $matches[1]), 0, 5);
        }

        return ['تحليل غير متوفر'];
    }

    /**
     * Format AI feedback for display
     */
    protected function formatAiFeedback(array $analysis): string
    {
        $feedback = "## التقييم: {$analysis['score']}/100\n\n";

        if (!empty($analysis['strengths'])) {
            $feedback .= "### نقاط القوة:\n";
            foreach ($analysis['strengths'] as $strength) {
                $feedback .= "- {$strength}\n";
            }
            $feedback .= "\n";
        }

        if (!empty($analysis['weaknesses'])) {
            $feedback .= "### نقاط الضعف:\n";
            foreach ($analysis['weaknesses'] as $weakness) {
                $feedback .= "- {$weakness}\n";
            }
            $feedback .= "\n";
        }

        if (!empty($analysis['recommendations'])) {
            $feedback .= "### توصيات التحسين:\n";
            foreach ($analysis['recommendations'] as $recommendation) {
                $feedback .= "- {$recommendation}\n";
            }
        }

        return $feedback;
    }
}
