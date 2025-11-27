<?php

namespace App\Services;

use App\Models\BusinessPlan;
use App\Models\WizardStep;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class WizardAISuggestionService
{
    protected string $baseUrl;
    protected string $model = 'gemma:2b';
    protected int $cacheMinutes = 30;

    public function __construct()
    {
        $this->baseUrl = config('ollama.base_url', 'http://localhost:11434');
    }

    /**
     * Generate AI suggestions for a specific wizard step field
     */
    public function generateFieldSuggestions(
        BusinessPlan $plan,
        WizardStep $step,
        string $fieldName,
        string $fieldLabel,
        array $previousAnswers = []
    ): array {
        $cacheKey = "ai_suggestions:{$plan->id}:{$step->id}:{$fieldName}";

        // Check cache first
        if (Cache::has($cacheKey) && !empty($previousAnswers)) {
            return Cache::get($cacheKey);
        }

        try {
            $prompt = $this->buildSuggestionPrompt($plan, $step, $fieldName, $fieldLabel, $previousAnswers);

            $response = $this->callOllama($prompt);

            $suggestions = $this->parseSuggestions($response);

            // Cache for 30 minutes
            Cache::put($cacheKey, $suggestions, now()->addMinutes($this->cacheMinutes));

            return $suggestions;

        } catch (Exception $e) {
            Log::error('AI Suggestion failed', [
                'plan_id' => $plan->id,
                'step_id' => $step->id,
                'field' => $fieldName,
                'error' => $e->getMessage(),
            ]);

            return $this->getFallbackSuggestions($step, $fieldName);
        }
    }

    /**
     * Generate suggestions for all fields in a step
     */
    public function generateStepSuggestions(
        BusinessPlan $plan,
        WizardStep $step,
        array $previousAnswers = []
    ): array {
        $suggestions = [];

        // Get fields from Bolt form or wizard questions
        $fields = $this->getStepFields($step);

        foreach ($fields as $field) {
            $suggestions[$field['name']] = $this->generateFieldSuggestions(
                $plan,
                $step,
                $field['name'],
                $field['label'],
                $previousAnswers
            );
        }

        return $suggestions;
    }

    /**
     * Build the AI prompt based on context
     */
    protected function buildSuggestionPrompt(
        BusinessPlan $plan,
        WizardStep $step,
        string $fieldName,
        string $fieldLabel,
        array $previousAnswers
    ): string {
        $prompt = "أنت مساعد ذكي متخصص في مساعدة رواد الأعمال في إعداد خطط الأعمال.\n\n";

        // Add project context
        $prompt .= "معلومات المشروع:\n";
        $prompt .= "- اسم الشركة: {$plan->company_name}\n";
        $prompt .= "- نوع المشروع: {$plan->project_type}\n";
        $prompt .= "- الصناعة: {$plan->industry_type}\n";

        if ($plan->vision) {
            $prompt .= "- الرؤية: {$plan->vision}\n";
        }

        // Add previous answers as context
        if (!empty($previousAnswers)) {
            $prompt .= "\nالإجابات السابقة:\n";
            foreach ($previousAnswers as $key => $value) {
                if (!empty($value) && is_string($value)) {
                    $prompt .= "- {$key}: {$value}\n";
                }
            }
        }

        // Add custom prompt from step if exists
        if ($step->ai_suggestion_prompt) {
            $prompt .= "\nتعليمات إضافية: {$step->ai_suggestion_prompt}\n";
        }

        // The actual question
        $prompt .= "\nالخطوة الحالية: {$step->title}\n";
        $prompt .= "السؤال: {$fieldLabel}\n\n";

        $prompt .= "قدم 3-5 اقتراحات قصيرة ومحددة للإجابة على هذا السؤال بناءً على سياق المشروع.\n";
        $prompt .= "اكتب كل اقتراح في سطر منفصل يبدأ بـ •\n";
        $prompt .= "كن محدداً وعملياً، واستخدم أمثلة حقيقية من السوق إن أمكن.";

        return $prompt;
    }

    /**
     * Call Ollama API
     */
    protected function callOllama(string $prompt): string
    {
        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            ]);

        if (!$response->successful()) {
            throw new Exception('Ollama API error: ' . $response->body());
        }

        return $response->json('response', '');
    }

    /**
     * Parse AI response into structured suggestions
     */
    protected function parseSuggestions(string $response): array
    {
        $suggestions = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);

            // Match lines starting with bullet points
            if (preg_match('/^[•\-\*]\s*(.+)/', $line, $matches)) {
                $suggestion = trim($matches[1]);
                if (strlen($suggestion) > 5) {
                    $suggestions[] = [
                        'text' => $suggestion,
                        'type' => 'ai',
                    ];
                }
            }
        }

        // If no bullet points found, try to split by sentences
        if (empty($suggestions) && strlen($response) > 20) {
            $sentences = preg_split('/[.،؛]\s+/', $response);
            foreach (array_slice($sentences, 0, 5) as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) > 10) {
                    $suggestions[] = [
                        'text' => $sentence,
                        'type' => 'ai',
                    ];
                }
            }
        }

        return array_slice($suggestions, 0, 5);
    }

    /**
     * Get fallback suggestions when AI fails
     */
    protected function getFallbackSuggestions(WizardStep $step, string $fieldName): array
    {
        $fallbacks = [
            'key_partners' => [
                ['text' => 'شركات الشحن والتوصيل', 'type' => 'fallback'],
                ['text' => 'بوابات الدفع الإلكتروني', 'type' => 'fallback'],
                ['text' => 'موردين المواد الخام', 'type' => 'fallback'],
            ],
            'value_proposition' => [
                ['text' => 'جودة عالية بأسعار تنافسية', 'type' => 'fallback'],
                ['text' => 'خدمة عملاء متميزة', 'type' => 'fallback'],
                ['text' => 'توصيل سريع وموثوق', 'type' => 'fallback'],
            ],
            'customer_segments' => [
                ['text' => 'الشباب من 18-35 سنة', 'type' => 'fallback'],
                ['text' => 'أصحاب المشاريع الصغيرة', 'type' => 'fallback'],
                ['text' => 'العائلات ذات الدخل المتوسط', 'type' => 'fallback'],
            ],
            'channels' => [
                ['text' => 'منصات التواصل الاجتماعي', 'type' => 'fallback'],
                ['text' => 'الموقع الإلكتروني والتطبيق', 'type' => 'fallback'],
                ['text' => 'الإعلانات المدفوعة', 'type' => 'fallback'],
            ],
            'revenue_streams' => [
                ['text' => 'مبيعات مباشرة', 'type' => 'fallback'],
                ['text' => 'اشتراكات شهرية', 'type' => 'fallback'],
                ['text' => 'عمولات على المعاملات', 'type' => 'fallback'],
            ],
        ];

        return $fallbacks[$fieldName] ?? [
            ['text' => 'اضغط للحصول على اقتراحات', 'type' => 'fallback'],
        ];
    }

    /**
     * Get fields from step (either Bolt form or wizard questions)
     */
    protected function getStepFields(WizardStep $step): array
    {
        $fields = [];

        if ($step->usesBoltForm()) {
            // Get fields from Bolt form
            $sections = $step->getBoltFormFields();
            foreach ($sections as $section) {
                foreach ($section->fields as $field) {
                    $fields[] = [
                        'name' => $field->name ?? $field->id,
                        'label' => $field->options['label'] ?? $field->name,
                    ];
                }
            }
        } else {
            // Get fields from wizard questions
            foreach ($step->activeQuestions as $question) {
                $fields[] = [
                    'name' => $question->field_name,
                    'label' => $question->label,
                ];
            }
        }

        return $fields;
    }

    /**
     * Clear cached suggestions for a plan
     */
    public function clearCache(BusinessPlan $plan): void
    {
        // Clear all cached suggestions for this plan
        Cache::forget("ai_suggestions:{$plan->id}:*");
    }
}
