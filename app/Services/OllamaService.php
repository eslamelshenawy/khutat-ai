<?php

namespace App\Services;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use App\Models\AiGeneration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class OllamaService
{
    /**
     * Ollama API base URL (running locally)
     */
    protected string $baseUrl;

    /**
     * Default model for content generation
     */
    protected string $defaultModel = 'gemma:2b';

    /**
     * Advanced model for complex analysis
     */
    protected string $advancedModel = 'gemma:2b';

    /**
     * Maximum retry attempts
     */
    protected int $maxRetries = 3;

    /**
     * Retry delay in seconds
     */
    protected int $retryDelay = 2;

    public function __construct()
    {
        $this->baseUrl = config('ollama.base_url', 'http://localhost:11434');
    }

    /**
     * Generate content for a business plan chapter
     *
     * @param Chapter $chapter
     * @param array $context
     * @return string
     * @throws Exception
     */
    public function generateChapterContent(Chapter $chapter, array $context = []): string
    {
        $startTime = microtime(true);

        try {
            // Check rate limit
            $this->handleRateLimit($chapter->businessPlan->user_id);

            // Build the prompt
            $prompt = $this->buildChapterPrompt($chapter, $context);

            // Select appropriate model
            $model = $this->selectModel($chapter);

            Log::info('Generating chapter content with Ollama', [
                'chapter_id' => $chapter->id,
                'chapter_type' => $chapter->chapter_type,
                'model' => $model,
            ]);

            // Call Ollama API with retry logic
            $response = $this->callOllamaWithRetry(function () use ($model, $prompt) {
                return $this->generate($model, $prompt);
            });

            $content = $response['response'] ?? '';
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log generation to database
            $this->logGeneration(
                $chapter,
                $prompt,
                $content,
                $model,
                $processingTime,
                'completed'
            );

            Log::info('Chapter content generated successfully', [
                'chapter_id' => $chapter->id,
                'processing_time_ms' => $processingTime,
            ]);

            return $content;

        } catch (Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;

            Log::error('Failed to generate chapter content', [
                'chapter_id' => $chapter->id,
                'error' => $e->getMessage(),
                'processing_time_ms' => $processingTime,
            ]);

            // Log failed generation
            $this->logGeneration(
                $chapter,
                $prompt ?? '',
                '',
                $model ?? $this->defaultModel,
                $processingTime,
                'failed',
                $e->getMessage()
            );

            throw new Exception('فشل توليد محتوى الفصل: ' . $e->getMessage());
        }
    }

    /**
     * Analyze the quality of a business plan
     *
     * @param BusinessPlan $plan
     * @return array
     * @throws Exception
     */
    public function analyzePlanQuality(BusinessPlan $plan): array
    {
        $startTime = microtime(true);

        try {
            // Check rate limit
            $this->handleRateLimit($plan->user_id);

            // Prepare plan content for analysis
            $planContent = $this->preparePlanForAnalysis($plan);

            $prompt = $this->buildAnalysisPrompt($plan, $planContent);

            Log::info('Analyzing plan quality with Ollama', [
                'plan_id' => $plan->id,
                'plan_title' => $plan->title,
            ]);

            $response = $this->callOllamaWithRetry(function () use ($prompt) {
                return $this->generate($this->advancedModel, $prompt);
            });

            $analysisResult = $this->parseAnalysisResponse($response['response'] ?? '');
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log to database
            AiGeneration::create([
                'business_plan_id' => $plan->id,
                'user_id' => auth()->id(),
                'generation_type' => 'analysis',
                'prompt' => $prompt,
                'response' => json_encode($analysisResult, JSON_UNESCAPED_UNICODE),
                'model_used' => $this->advancedModel,
                'tokens_used' => 0, // Ollama doesn't return token count
                'cost' => 0, // Free!
                'processing_time_ms' => $processingTime,
                'status' => 'completed',
            ]);

            Log::info('Plan quality analyzed successfully', [
                'plan_id' => $plan->id,
                'score' => $analysisResult['score'],
            ]);

            return $analysisResult;

        } catch (Exception $e) {
            Log::error('Failed to analyze plan quality', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('فشل تحليل جودة الخطة: ' . $e->getMessage());
        }
    }

    /**
     * Generate recommendations for improving the business plan
     *
     * @param BusinessPlan $plan
     * @return array
     * @throws Exception
     */
    public function generateRecommendations(BusinessPlan $plan): array
    {
        $startTime = microtime(true);

        try {
            // Check rate limit
            $this->handleRateLimit($plan->user_id);

            $planContent = $this->preparePlanForAnalysis($plan);
            $prompt = $this->buildRecommendationsPrompt($plan, $planContent);

            Log::info('Generating recommendations with Ollama', [
                'plan_id' => $plan->id,
            ]);

            $response = $this->callOllamaWithRetry(function () use ($prompt) {
                return $this->generate($this->advancedModel, $prompt);
            });

            $recommendations = $this->parseRecommendations($response['response'] ?? '');
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log to database
            AiGeneration::create([
                'business_plan_id' => $plan->id,
                'user_id' => auth()->id(),
                'generation_type' => 'recommendations',
                'prompt' => $prompt,
                'response' => json_encode($recommendations, JSON_UNESCAPED_UNICODE),
                'model_used' => $this->advancedModel,
                'tokens_used' => 0,
                'cost' => 0,
                'processing_time_ms' => $processingTime,
                'status' => 'completed',
            ]);

            Log::info('Recommendations generated successfully', [
                'plan_id' => $plan->id,
                'recommendations_count' => count($recommendations),
            ]);

            return $recommendations;

        } catch (Exception $e) {
            Log::error('Failed to generate recommendations', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('فشل توليد التوصيات: ' . $e->getMessage());
        }
    }

    /**
     * Improve existing content based on instructions
     *
     * @param string $content
     * @param string $instructions
     * @return string
     * @throws Exception
     */
    public function improveContent(string $content, string $instructions): string
    {
        try {
            $prompt = "المحتوى الأصلي:\n{$content}\n\nتعليمات التحسين:\n{$instructions}\n\nقم بتحسين المحتوى وفقاً للتعليمات المذكورة مع الحفاظ على الهيكل العام والمعلومات المهمة.";

            Log::info('Improving content with Ollama', [
                'content_length' => strlen($content),
                'instructions' => $instructions,
            ]);

            $response = $this->callOllamaWithRetry(function () use ($prompt) {
                return $this->generate($this->defaultModel, $prompt);
            });

            $improvedContent = $response['response'] ?? '';

            Log::info('Content improved successfully');

            return $improvedContent;

        } catch (Exception $e) {
            Log::error('Failed to improve content', [
                'error' => $e->getMessage(),
            ]);

            throw new Exception('فشل تحسين المحتوى: ' . $e->getMessage());
        }
    }

    /**
     * Chat with AI about the business plan
     *
     * @param string $message
     * @param array $context
     * @return string
     * @throws Exception
     */
    public function chatWithAI(string $message, array $context = []): string
    {
        try {
            $systemPrompt = 'أنت مساعد ذكي متخصص في مساعدة رواد الأعمال في إعداد خطط الأعمال. أجب بشكل مفيد واحترافي.';

            $prompt = $systemPrompt . "\n\n" . $message;

            Log::info('Chat with Ollama AI', [
                'message_length' => strlen($message),
            ]);

            $response = $this->generate($this->defaultModel, $prompt);

            $reply = $response['response'] ?? '';

            Log::info('AI chat response generated');

            return $reply;

        } catch (Exception $e) {
            Log::error('Failed to chat with AI', [
                'error' => $e->getMessage(),
            ]);

            throw new Exception('فشل الاتصال بالذكاء الاصطناعي: ' . $e->getMessage());
        }
    }

    /**
     * Generate text using Ollama API
     *
     * @param string $model
     * @param string $prompt
     * @return array
     * @throws Exception
     */
    protected function generate(string $model, string $prompt): array
    {
        $response = Http::timeout(120)
            ->post("{$this->baseUrl}/api/generate", [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'top_k' => 40,
                ]
            ]);

        if (!$response->successful()) {
            throw new Exception('Ollama API error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Handle rate limiting for API calls
     *
     * @param int $userId
     * @throws Exception
     */
    protected function handleRateLimit(int $userId): void
    {
        $cacheKey = "ollama_rate_limit:user:{$userId}";
        $limit = config('ollama.rate_limit_per_minute', 30); // More generous for local
        $ttl = 60; // 1 minute

        $currentCount = Cache::get($cacheKey, 0);

        if ($currentCount >= $limit) {
            Log::warning('Rate limit exceeded', [
                'user_id' => $userId,
                'current_count' => $currentCount,
                'limit' => $limit,
            ]);

            throw new Exception('تم تجاوز الحد المسموح من طلبات الذكاء الاصطناعي. الرجاء المحاولة بعد دقيقة.');
        }

        Cache::put($cacheKey, $currentCount + 1, $ttl);
    }

    /**
     * Build prompt for chapter content generation (same as OpenAI)
     */
    protected function buildChapterPrompt(Chapter $chapter, array $context): string
    {
        $plan = $chapter->businessPlan;

        $prompt = "أكتب محتوى احترافي لفصل '{$chapter->title}' في خطة عمل.\n\n";

        $prompt .= "معلومات المشروع:\n";
        $prompt .= "- الاسم: {$plan->company_name}\n";
        $prompt .= "- نوع المشروع: {$plan->project_type}\n";
        $prompt .= "- الصناعة: {$plan->industry_type}\n";

        if ($plan->vision) {
            $prompt .= "- الرؤية: {$plan->vision}\n";
        }

        if ($plan->mission) {
            $prompt .= "- الرسالة: {$plan->mission}\n";
        }

        if (!empty($context)) {
            $prompt .= "\nمعلومات إضافية:\n";
            foreach ($context as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $prompt .= "- {$key}: {$value}\n";
            }
        }

        // Add specific instructions based on chapter type
        $prompt .= "\n" . $this->getChapterSpecificInstructions($chapter->chapter_type);

        $prompt .= "\n\nاكتب محتوى احترافياً ومقنعاً باللغة العربية بحدود 500-800 كلمة. استخدم أسلوباً واضحاً ومنظماً مع عناوين فرعية مناسبة.";

        return $prompt;
    }

    /**
     * Get chapter-specific instructions (same as OpenAI)
     */
    protected function getChapterSpecificInstructions(string $chapterType): string
    {
        $instructions = [
            'executive_summary' => 'اكتب ملخصاً تنفيذياً شاملاً يتضمن: نظرة عامة عن المشروع، الفرصة السوقية، الميزة التنافسية، الأهداف المالية الرئيسية، وملخص الفريق.',
            'company_description' => 'صف الشركة بشكل مفصل يتضمن: تاريخ التأسيس، الهيكل القانوني، الموقع، المنتجات أو الخدمات الرئيسية، والميزة التنافسية الفريدة.',
            'market_analysis' => 'قدم تحليلاً سوقياً شاملاً يشمل: حجم السوق المستهدف، اتجاهات السوق، الفئة المستهدفة، التحليل التنافسي، والفرص المتاحة.',
            // ... (same as OpenAI Service)
        ];

        return $instructions[$chapterType] ?? 'اكتب محتوى احترافياً ومفصلاً للفصل يتضمن جميع المعلومات الضرورية بأسلوب واضح ومنظم.';
    }

    /**
     * Build prompt for quality analysis
     */
    protected function buildAnalysisPrompt(BusinessPlan $plan, string $planContent): string
    {
        return "قم بتحليل خطة العمل التالية وقيّمها من حيث:\n" .
               "1. الوضوح والشمولية (من 20)\n" .
               "2. جودة المحتوى والتفاصيل (من 20)\n" .
               "3. الجدوى المالية والتوقعات (من 20)\n" .
               "4. التحليل السوقي والتنافسي (من 20)\n" .
               "5. خطة التنفيذ والواقعية (من 20)\n\n" .
               "معلومات الخطة:\n" .
               "- العنوان: {$plan->title}\n" .
               "- الصناعة: {$plan->industry_type}\n" .
               "- نوع المشروع: {$plan->project_type}\n\n" .
               "المحتوى:\n{$planContent}\n\n" .
               "قدم تقييماً رقمياً شاملاً (من 100) مع تفصيل النقاط لكل معيار، نقاط القوة، نقاط الضعف، والتوصيات الرئيسية.";
    }

    /**
     * Build prompt for recommendations
     */
    protected function buildRecommendationsPrompt(BusinessPlan $plan, string $planContent): string
    {
        return "بناءً على خطة العمل التالية، قدم توصيات محددة وعملية لتحسينها:\n\n" .
               "معلومات الخطة:\n" .
               "- العنوان: {$plan->title}\n" .
               "- الصناعة: {$plan->industry_type}\n" .
               "- نوع المشروع: {$plan->project_type}\n\n" .
               "المحتوى:\n{$planContent}\n\n" .
               "قدم 5-8 توصيات محددة تغطي:\n" .
               "- تحسينات على الاستراتيجية\n" .
               "- فرص نمو إضافية\n" .
               "- تحسينات على الخطة المالية\n" .
               "- تعزيز الميزة التنافسية\n" .
               "- تحسينات عملية أخرى\n\n" .
               "لكل توصية، حدد: العنوان، الوصف المفصل، الأولوية (عالية/متوسطة/منخفضة)، والتأثير المتوقع.";
    }

    /**
     * Select appropriate model based on chapter importance
     */
    protected function selectModel(Chapter $chapter): string
    {
        $importantChapters = [
            'executive_summary',
            'market_analysis',
            'financial_plan',
            'business_model',
        ];

        return in_array($chapter->chapter_type, $importantChapters)
            ? $this->advancedModel
            : $this->defaultModel;
    }

    /**
     * Prepare business plan content for analysis
     */
    protected function preparePlanForAnalysis(BusinessPlan $plan): string
    {
        $content = "خطة العمل: {$plan->title}\n\n";

        $content .= "معلومات أساسية:\n";
        $content .= "الشركة: {$plan->company_name}\n";
        $content .= "الصناعة: {$plan->industry_type}\n";
        $content .= "نوع المشروع: {$plan->project_type}\n";

        if ($plan->vision) {
            $content .= "الرؤية: {$plan->vision}\n";
        }

        if ($plan->mission) {
            $content .= "الرسالة: {$plan->mission}\n";
        }

        $content .= "\nالفصول:\n";

        foreach ($plan->chapters as $chapter) {
            if (!empty($chapter->content)) {
                $content .= "\n--- {$chapter->title} ---\n";
                $content .= strip_tags($chapter->content) . "\n";
            }
        }

        // Limit content length
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . "\n... (محتوى مختصر)";
        }

        return $content;
    }

    /**
     * Parse analysis response from AI
     */
    protected function parseAnalysisResponse(string $response): array
    {
        // Try to extract score
        preg_match('/(\d+)\s*\/\s*100/', $response, $matches);
        $score = isset($matches[1]) ? (int)$matches[1] : 70;

        return [
            'score' => $score,
            'feedback' => $response,
            'analyzed_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Parse recommendations from AI response
     */
    protected function parseRecommendations(string $response): array
    {
        $recommendations = [];

        // Split response into lines
        $lines = explode("\n", $response);
        $currentRecommendation = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Check if this is a new recommendation
            if (preg_match('/^(\d+[\.\)]|[-*•])\s*(.+)/', $line, $matches)) {
                if ($currentRecommendation) {
                    $recommendations[] = $currentRecommendation;
                }

                $currentRecommendation = [
                    'title' => trim($matches[2]),
                    'description' => '',
                    'priority' => 'medium',
                ];
            } elseif ($currentRecommendation && !empty($line)) {
                // Add to description
                $currentRecommendation['description'] .= $line . ' ';

                // Check for priority keywords
                if (stripos($line, 'عالية') !== false || stripos($line, 'مهم') !== false) {
                    $currentRecommendation['priority'] = 'high';
                } elseif (stripos($line, 'منخفضة') !== false) {
                    $currentRecommendation['priority'] = 'low';
                }
            }
        }

        if ($currentRecommendation) {
            $recommendations[] = $currentRecommendation;
        }

        // If parsing failed, return the whole response as one recommendation
        if (empty($recommendations)) {
            $recommendations[] = [
                'title' => 'توصيات عامة',
                'description' => $response,
                'priority' => 'medium',
            ];
        }

        return $recommendations;
    }

    /**
     * Log generation to database
     */
    protected function logGeneration(
        Chapter $chapter,
        string $prompt,
        string $response,
        string $model,
        float $processingTime,
        string $status = 'completed',
        ?string $errorMessage = null
    ): void {
        AiGeneration::create([
            'business_plan_id' => $chapter->business_plan_id,
            'chapter_id' => $chapter->id,
            'user_id' => auth()->id(),
            'generation_type' => 'chapter',
            'prompt' => $prompt,
            'response' => $response,
            'model_used' => $model,
            'tokens_used' => 0, // Ollama doesn't track tokens
            'cost' => 0, // Free!
            'processing_time_ms' => (int)$processingTime,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Call Ollama API with retry logic
     */
    protected function callOllamaWithRetry(callable $apiCall)
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                return $apiCall();

            } catch (Exception $e) {
                $lastException = $e;

                Log::warning('Ollama API call failed', [
                    'attempt' => $attempt,
                    'max_retries' => $this->maxRetries,
                    'error' => $e->getMessage(),
                ]);

                // Wait before retry (exponential backoff)
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay * $attempt);
                }
            }
        }

        throw $lastException;
    }
}
