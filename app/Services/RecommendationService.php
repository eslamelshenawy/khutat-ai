<?php

namespace App\Services;

use App\Models\BusinessPlan;
use App\Models\AiRecommendation;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * Generate recommendations for a business plan
     */
    public function generateRecommendations(BusinessPlan $plan): array
    {
        $plan->load(['chapters', 'template']);

        $recommendations = [];

        try {
            // Generate different types of recommendations
            $recommendations[] = $this->generateContentRecommendations($plan);
            $recommendations[] = $this->generateFinancialRecommendations($plan);
            $recommendations[] = $this->generateMarketingRecommendations($plan);
            $recommendations[] = $this->generateOperationalRecommendations($plan);

            // Flatten and save recommendations
            $savedRecommendations = [];
            foreach ($recommendations as $categoryRecs) {
                foreach ($categoryRecs as $rec) {
                    $saved = $this->saveRecommendation($plan, $rec);
                    if ($saved) {
                        $savedRecommendations[] = $saved;
                    }
                }
            }

            return $savedRecommendations;

        } catch (\Exception $e) {
            Log::error('Recommendation Generation Error', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Generate content quality recommendations
     */
    protected function generateContentRecommendations(BusinessPlan $plan): array
    {
        $recommendations = [];

        // Check for incomplete chapters
        $incompleteChapters = $plan->chapters()
            ->where(function ($query) {
                $query->whereNull('content')
                    ->orWhere('content', '')
                    ->orWhereRaw('LENGTH(content) < 100');
            })
            ->get();

        if ($incompleteChapters->count() > 0) {
            $chapterTitles = $incompleteChapters->pluck('title')->implode('، ');
            $recommendations[] = [
                'type' => 'content_quality',
                'category' => 'محتوى الخطة',
                'priority' => 'high',
                'title' => 'فصول غير مكتملة',
                'description' => "يوجد فصول تحتاج إلى استكمال المحتوى: {$chapterTitles}",
                'action_items' => [
                    'قم بمراجعة الفصول المذكورة',
                    'أضف محتوى تفصيلي لكل فصل',
                    'تأكد من أن كل فصل يحتوي على 200 كلمة على الأقل'
                ],
                'confidence_score' => 95,
            ];
        }

        // Check content length
        $totalWords = 0;
        foreach ($plan->chapters as $chapter) {
            $totalWords += str_word_count(strip_tags($chapter->content ?? ''));
        }

        if ($totalWords < 2000) {
            $recommendations[] = [
                'type' => 'content_quality',
                'category' => 'محتوى الخطة',
                'priority' => 'medium',
                'title' => 'محتوى الخطة قصير',
                'description' => "خطة العمل تحتوي على {$totalWords} كلمة فقط. يُنصح بأن تحتوي الخطة على 3000-5000 كلمة على الأقل لتكون شاملة.",
                'action_items' => [
                    'قم بتوسيع المحتوى في كل فصل',
                    'أضف تفاصيل أكثر عن المنتج/الخدمة',
                    'قم بتحليل السوق والمنافسين بشكل أعمق'
                ],
                'confidence_score' => 85,
            ];
        }

        return $recommendations;
    }

    /**
     * Generate financial recommendations using AI
     */
    protected function generateFinancialRecommendations(BusinessPlan $plan): array
    {
        $recommendations = [];

        $prompt = "قم بتحليل خطة العمل التالية وقدم 3 توصيات مالية محددة:\n\n";
        $prompt .= "نوع المشروع: {$plan->project_type}\n";
        $prompt .= "الصناعة: {$plan->industry_type}\n";
        $prompt .= "رأس المال المطلوب: {$plan->estimated_budget}\n\n";

        // Get financial chapter content if exists
        $financialChapter = $plan->chapters()->where('title', 'like', '%مالي%')->first();
        if ($financialChapter && $financialChapter->content) {
            $prompt .= "الخطة المالية:\n" . substr($financialChapter->content, 0, 500) . "\n\n";
        }

        $prompt .= "قدم 3 توصيات مالية في شكل JSON array:\n";
        $prompt .= '{"recommendations": [{"title": "عنوان", "description": "وصف", "priority": "high/medium/low", "action_items": ["خطوة 1", "خطوة 2"]}]}';

        try {
            $response = $this->ollamaService->generateText($prompt, [
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            // Try to parse JSON response
            $decoded = json_decode($response, true);
            if ($decoded && isset($decoded['recommendations'])) {
                foreach ($decoded['recommendations'] as $rec) {
                    $recommendations[] = [
                        'type' => 'financial_planning',
                        'category' => 'التخطيط المالي',
                        'priority' => $rec['priority'] ?? 'medium',
                        'title' => $rec['title'] ?? 'توصية مالية',
                        'description' => $rec['description'] ?? '',
                        'action_items' => $rec['action_items'] ?? [],
                        'confidence_score' => 75,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Financial Recommendations AI Error', [
                'error' => $e->getMessage(),
            ]);
        }

        return $recommendations;
    }

    /**
     * Generate marketing recommendations
     */
    protected function generateMarketingRecommendations(BusinessPlan $plan): array
    {
        $recommendations = [];

        // Check for marketing chapter
        $marketingChapter = $plan->chapters()->where('title', 'like', '%تسويق%')->first();

        if (!$marketingChapter || empty($marketingChapter->content)) {
            $recommendations[] = [
                'type' => 'marketing_strategy',
                'category' => 'استراتيجية التسويق',
                'priority' => 'high',
                'title' => 'استراتيجية التسويق مفقودة',
                'description' => 'خطة العمل لا تحتوي على استراتيجية تسويقية واضحة. التسويق ضروري لنجاح المشروع.',
                'action_items' => [
                    'حدد السوق المستهدف والشريحة المستهدفة',
                    'قم بتحليل المنافسين ونقاط القوة والضعف',
                    'ضع خطة للترويج والإعلان',
                    'حدد قنوات التسويق المناسبة (رقمي، تقليدي)'
                ],
                'confidence_score' => 90,
            ];
        } else {
            // Analyze marketing content for digital presence
            $content = strtolower($marketingChapter->content);
            $hasDigitalMarketing = str_contains($content, 'رقمي') ||
                                   str_contains($content, 'سوشيال') ||
                                   str_contains($content, 'إنترنت') ||
                                   str_contains($content, 'موقع');

            if (!$hasDigitalMarketing) {
                $recommendations[] = [
                    'type' => 'marketing_strategy',
                    'category' => 'استراتيجية التسويق',
                    'priority' => 'medium',
                    'title' => 'تطوير التسويق الرقمي',
                    'description' => 'ننصح بإضافة استراتيجية للتسويق الرقمي والتواجد الإلكتروني لمشروعك.',
                    'action_items' => [
                        'أنشئ موقع إلكتروني احترافي',
                        'استخدم وسائل التواصل الاجتماعي',
                        'فكر في الإعلانات المدفوعة عبر الإنترنت',
                        'استخدم التسويق بالمحتوى والـ SEO'
                    ],
                    'confidence_score' => 80,
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Generate operational recommendations
     */
    protected function generateOperationalRecommendations(BusinessPlan $plan): array
    {
        $recommendations = [];

        // Check if execution plan exists
        $executionChapter = $plan->chapters()->where('title', 'like', '%تنفيذ%')->first();

        if (!$executionChapter || empty($executionChapter->content)) {
            $recommendations[] = [
                'type' => 'operations',
                'category' => 'العمليات التشغيلية',
                'priority' => 'high',
                'title' => 'خطة التنفيذ مفقودة',
                'description' => 'يجب وضع خطة تنفيذية واضحة تحدد الخطوات والجدول الزمني.',
                'action_items' => [
                    'حدد مراحل التنفيذ الرئيسية',
                    'ضع جدول زمني لكل مرحلة',
                    'حدد الموارد المطلوبة لكل مرحلة',
                    'عين المسؤوليات على أعضاء الفريق'
                ],
                'confidence_score' => 85,
            ];
        }

        // Check for risk analysis
        $riskChapter = $plan->chapters()->where('title', 'like', '%مخاطر%')->first();

        if (!$riskChapter || empty($riskChapter->content)) {
            $recommendations[] = [
                'type' => 'risk_management',
                'category' => 'إدارة المخاطر',
                'priority' => 'medium',
                'title' => 'تحليل المخاطر مطلوب',
                'description' => 'يجب تحديد المخاطر المحتملة ووضع خطط للتعامل معها.',
                'action_items' => [
                    'حدد المخاطر المحتملة للمشروع',
                    'قيّم احتمالية وتأثير كل خطر',
                    'ضع خطط للوقاية من المخاطر',
                    'أعد خطط بديلة للطوارئ'
                ],
                'confidence_score' => 80,
            ];
        }

        return $recommendations;
    }

    /**
     * Save recommendation to database
     */
    protected function saveRecommendation(BusinessPlan $plan, array $data): ?AiRecommendation
    {
        try {
            return AiRecommendation::create([
                'business_plan_id' => $plan->id,
                'user_id' => $plan->user_id,
                'type' => $data['type'],
                'category' => $data['category'],
                'priority' => $data['priority'],
                'title' => $data['title'],
                'description' => $data['description'],
                'action_items' => $data['action_items'],
                'confidence_score' => $data['confidence_score'],
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            Log::error('Save Recommendation Error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return null;
        }
    }

    /**
     * Get recommendations for a business plan
     */
    public function getRecommendations(BusinessPlan $plan, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $plan->aiRecommendations();

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('priority', 'desc')
                    ->orderBy('confidence_score', 'desc')
                    ->get();
    }

    /**
     * Mark recommendation as implemented
     */
    public function markAsImplemented(AiRecommendation $recommendation): bool
    {
        return $recommendation->update([
            'status' => 'implemented',
            'implemented_at' => now(),
        ]);
    }

    /**
     * Dismiss recommendation
     */
    public function dismissRecommendation(AiRecommendation $recommendation, ?string $reason = null): bool
    {
        return $recommendation->update([
            'status' => 'dismissed',
            'dismissed_at' => now(),
            'dismissed_reason' => $reason,
        ]);
    }
}
