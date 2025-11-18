<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\BusinessPlan;
use App\Models\Chapter;
use App\Models\AiGeneration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class OpenAIService
{
    /**
     * Default model for general content generation
     */
    protected string $defaultModel = 'gpt-3.5-turbo';

    /**
     * Advanced model for complex analysis and important chapters
     */
    protected string $advancedModel = 'gpt-4-turbo-preview';

    /**
     * Maximum retry attempts for failed requests
     */
    protected int $maxRetries = 3;

    /**
     * Retry delay in seconds
     */
    protected int $retryDelay = 2;

    /**
     * Generate content for a business plan chapter
     *
     * @param Chapter $chapter
     * @param array $context Additional context for generation
     * @return string Generated content
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

            Log::info('Generating chapter content', [
                'chapter_id' => $chapter->id,
                'chapter_type' => $chapter->chapter_type,
                'model' => $model,
            ]);

            // Call OpenAI API with retry logic
            $response = $this->callOpenAIWithRetry(function () use ($model, $prompt) {
                return OpenAI::chat()->create([
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);
            });

            $content = $response->choices[0]->message->content;
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log generation to database
            $this->logGeneration(
                $chapter,
                $prompt,
                $content,
                $response,
                $processingTime,
                'completed'
            );

            Log::info('Chapter content generated successfully', [
                'chapter_id' => $chapter->id,
                'tokens_used' => $response->usage->totalTokens,
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
                null,
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
     * @return array Analysis results with score and feedback
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

            Log::info('Analyzing plan quality', [
                'plan_id' => $plan->id,
                'plan_title' => $plan->title,
            ]);

            $response = $this->callOpenAIWithRetry(function () use ($prompt) {
                return OpenAI::chat()->create([
                    'model' => $this->advancedModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت خبير متخصص في تحليل وتقييم خطط الأعمال. قدم تحليلاً شاملاً ومفصلاً مع تقييم رقمي دقيق.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                ]);
            });

            $analysisResult = $this->parseAnalysisResponse($response->choices[0]->message->content);
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log to database
            AiGeneration::create([
                'business_plan_id' => $plan->id,
                'user_id' => auth()->id(),
                'generation_type' => 'quality_analysis',
                'prompt' => $prompt,
                'response' => json_encode($analysisResult, JSON_UNESCAPED_UNICODE),
                'model_used' => $this->advancedModel,
                'tokens_used' => $response->usage->totalTokens,
                'cost' => $this->calculateCost($response->usage->totalTokens, $this->advancedModel),
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
     * @return array Array of recommendations
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

            Log::info('Generating recommendations', [
                'plan_id' => $plan->id,
            ]);

            $response = $this->callOpenAIWithRetry(function () use ($prompt) {
                return OpenAI::chat()->create([
                    'model' => $this->advancedModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت مستشار أعمال خبير. قدم توصيات عملية ومحددة لتحسين خطط الأعمال.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1500,
                ]);
            });

            $recommendations = $this->parseRecommendations($response->choices[0]->message->content);
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log to database
            AiGeneration::create([
                'business_plan_id' => $plan->id,
                'user_id' => auth()->id(),
                'generation_type' => 'recommendations',
                'prompt' => $prompt,
                'response' => json_encode($recommendations, JSON_UNESCAPED_UNICODE),
                'model_used' => $this->advancedModel,
                'tokens_used' => $response->usage->totalTokens,
                'cost' => $this->calculateCost($response->usage->totalTokens, $this->advancedModel),
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
     * @param string $content Original content
     * @param string $instructions Improvement instructions
     * @return string Improved content
     * @throws Exception
     */
    public function improveContent(string $content, string $instructions): string
    {
        try {
            $prompt = "المحتوى الأصلي:\n{$content}\n\nتعليمات التحسين:\n{$instructions}\n\nقم بتحسين المحتوى وفقاً للتعليمات المذكورة مع الحفاظ على الهيكل العام والمعلومات المهمة.";

            Log::info('Improving content', [
                'content_length' => strlen($content),
                'instructions' => $instructions,
            ]);

            $response = $this->callOpenAIWithRetry(function () use ($prompt) {
                return OpenAI::chat()->create([
                    'model' => $this->defaultModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'أنت محرر محترف متخصص في تحسين النصوص العربية. حافظ على الأسلوب الاحترافي والوضوح.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);
            });

            $improvedContent = $response->choices[0]->message->content;

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
     * @param string $message User message
     * @param array $context Conversation context
     * @return string AI response
     * @throws Exception
     */
    public function chatWithAI(string $message, array $context = []): string
    {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'أنت مساعد ذكي متخصص في مساعدة رواد الأعمال في إعداد خطط الأعمال. أجب بشكل مفيد واحترافي.'
                ]
            ];

            // Add context messages if available
            foreach ($context as $contextMessage) {
                $messages[] = $contextMessage;
            }

            // Add user message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            Log::info('Chat with AI', [
                'message_length' => strlen($message),
                'context_messages' => count($context),
            ]);

            $response = $this->callOpenAIWithRetry(function () use ($messages) {
                return OpenAI::chat()->create([
                    'model' => $this->defaultModel,
                    'messages' => $messages,
                    'temperature' => 0.8,
                    'max_tokens' => 1000,
                ]);
            });

            $reply = $response->choices[0]->message->content;

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
     * Handle rate limiting for API calls
     *
     * @param int $userId
     * @throws Exception
     */
    public function handleRateLimit(int $userId): void
    {
        $cacheKey = "openai_rate_limit:user:{$userId}";
        $limit = config('openai.rate_limit_per_minute', 10);
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
     * Build prompt for chapter content generation
     *
     * @param Chapter $chapter
     * @param array $context
     * @return string
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
     * Get specific instructions based on chapter type
     *
     * @param string $chapterType
     * @return string
     */
    protected function getChapterSpecificInstructions(string $chapterType): string
    {
        $instructions = [
            'executive_summary' => 'اكتب ملخصاً تنفيذياً شاملاً يتضمن: نظرة عامة عن المشروع، الفرصة السوقية، الميزة التنافسية، الأهداف المالية الرئيسية، وملخص الفريق.',

            'company_description' => 'صف الشركة بشكل مفصل يتضمن: تاريخ التأسيس، الهيكل القانوني، الموقع، المنتجات أو الخدمات الرئيسية، والميزة التنافسية الفريدة.',

            'market_analysis' => 'قدم تحليلاً سوقياً شاملاً يشمل: حجم السوق المستهدف، اتجاهات السوق، الفئة المستهدفة، التحليل التنافسي، والفرص المتاحة.',

            'products_services' => 'صف المنتجات أو الخدمات بالتفصيل مع توضيح: المواصفات، الفوائد الرئيسية، مراحل التطوير، والميزة التنافسية لكل منتج/خدمة.',

            'marketing_strategy' => 'اكتب استراتيجية تسويقية متكاملة تتضمن: التموضع في السوق، استراتيجية التسعير، قنوات التوزيع، الترويج والإعلان، واستراتيجية النمو.',

            'operations_plan' => 'صف خطة العمليات بالتفصيل مع: سير العمليات اليومية، المرافق والمعدات المطلوبة، التكنولوجيا المستخدمة، وإدارة سلسلة التوريد.',

            'management_team' => 'قدم معلومات عن فريق الإدارة تشمل: الأدوار الرئيسية، المؤهلات والخبرات، الهيكل التنظيمي، والمستشارين أو أعضاء مجلس الإدارة.',

            'financial_plan' => 'اكتب خطة مالية شاملة تتضمن: متطلبات رأس المال، التوقعات المالية (الإيرادات والمصروفات)، تحليل التعادل، مصادر التمويل، والعائد المتوقع على الاستثمار.',

            'implementation_timeline' => 'ضع جدولاً زمنياً واضحاً للتنفيذ يشمل: المراحل الرئيسية، المهام الأساسية، الموارد المطلوبة لكل مرحلة، والإطار الزمني المتوقع.',

            'risk_analysis' => 'قدم تحليلاً للمخاطر يتضمن: تحديد المخاطر المحتملة (مالية، تشغيلية، سوقية)، تقييم احتمالية وتأثير كل خطر، واستراتيجيات التخفيف.',
        ];

        return $instructions[$chapterType] ?? 'اكتب محتوى احترافياً ومفصلاً للفصل يتضمن جميع المعلومات الضرورية بأسلوب واضح ومنظم.';
    }

    /**
     * Build prompt for quality analysis
     *
     * @param BusinessPlan $plan
     * @param string $planContent
     * @return string
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
     *
     * @param BusinessPlan $plan
     * @param string $planContent
     * @return string
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
     *
     * @param Chapter $chapter
     * @return string
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
     * Get system prompt for content generation
     *
     * @return string
     */
    protected function getSystemPrompt(): string
    {
        return "أنت مستشار أعمال محترف متخصص في كتابة خطط الأعمال الاحترافية. " .
               "لديك خبرة واسعة في مختلف الصناعات والأسواق. " .
               "تكتب بأسلوب احترافي، واضح، ومقنع باللغة العربية الفصحى. " .
               "تستخدم البيانات والأرقام عندما يكون ذلك مناسباً، وتقدم محتوى عملي وواقعي.";
    }

    /**
     * Prepare business plan content for analysis
     *
     * @param BusinessPlan $plan
     * @return string
     */
    protected function preparePlanForAnalysis(BusinessPlan $plan): string
    {
        $content = "خطة العمل: {$plan->title}\n\n";

        // Add basic information
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

        // Add chapters content
        foreach ($plan->chapters as $chapter) {
            if (!empty($chapter->content)) {
                $content .= "\n--- {$chapter->title} ---\n";
                $content .= strip_tags($chapter->content) . "\n";
            }
        }

        // Limit content length for API
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . "\n... (محتوى مختصر)";
        }

        return $content;
    }

    /**
     * Parse analysis response from AI
     *
     * @param string $response
     * @return array
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
     *
     * @param string $response
     * @return array
     */
    protected function parseRecommendations(string $response): array
    {
        $recommendations = [];

        // Split response into lines and look for recommendation patterns
        $lines = explode("\n", $response);
        $currentRecommendation = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Check if this is a new recommendation (numbered or bullet point)
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
     *
     * @param Chapter $chapter
     * @param string $prompt
     * @param string $response
     * @param mixed $apiResponse
     * @param float $processingTime
     * @param string $status
     * @param string|null $errorMessage
     */
    protected function logGeneration(
        Chapter $chapter,
        string $prompt,
        string $response,
        $apiResponse,
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
            'model_used' => $apiResponse->model ?? $this->defaultModel,
            'tokens_used' => $apiResponse->usage->totalTokens ?? 0,
            'cost' => $apiResponse ? $this->calculateCost($apiResponse->usage->totalTokens, $apiResponse->model) : 0,
            'processing_time_ms' => (int)$processingTime,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Calculate cost based on tokens and model
     *
     * @param int $tokens
     * @param string $model
     * @return float
     */
    protected function calculateCost(int $tokens, string $model): float
    {
        // Pricing per 1K tokens (as of 2024)
        $pricing = [
            'gpt-4-turbo-preview' => 0.01,
            'gpt-4-turbo' => 0.01,
            'gpt-4' => 0.03,
            'gpt-3.5-turbo' => 0.002,
        ];

        $pricePerThousand = $pricing[$model] ?? 0.002;

        return ($pricePerThousand / 1000) * $tokens;
    }

    /**
     * Call OpenAI API with retry logic
     *
     * @param callable $apiCall
     * @return mixed
     * @throws Exception
     */
    protected function callOpenAIWithRetry(callable $apiCall)
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                return $apiCall();

            } catch (Exception $e) {
                $lastException = $e;

                Log::warning('OpenAI API call failed', [
                    'attempt' => $attempt,
                    'max_retries' => $this->maxRetries,
                    'error' => $e->getMessage(),
                ]);

                // Don't retry on certain errors
                if (stripos($e->getMessage(), 'rate limit') !== false ||
                    stripos($e->getMessage(), 'quota') !== false) {
                    throw $e;
                }

                // Wait before retry (exponential backoff)
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay * $attempt);
                }
            }
        }

        throw $lastException;
    }
}
