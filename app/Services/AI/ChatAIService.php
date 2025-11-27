<?php

namespace App\Services\AI;

use App\Services\OllamaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Chat AI Service
 * Handles AI chat interactions with performance optimization and fallback mechanisms
 *
 * Non-Functional Requirements:
 * - Performance: Response time ≤ 2 seconds
 * - Reliability: Fallback mechanism on service failure
 * - Scalability: Support 100 concurrent chat sessions
 * - Maintainability: Separated logic from presentation
 */
class ChatAIService
{
    protected OllamaService $ollamaService;
    protected int $timeout = 2; // 2 seconds max response time
    protected array $fallbackResponses;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
        $this->fallbackResponses = $this->loadFallbackResponses();
    }

    /**
     * Process chat message with performance optimization
     */
    public function processMessage(string $message, string $context = 'general'): array
    {
        $startTime = microtime(true);

        try {
            // Check cache first for common questions (Performance optimization)
            $cacheKey = $this->getCacheKey($message, $context);
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse) {
                Log::info('Chat response served from cache', [
                    'message_hash' => md5($message),
                    'context' => $context,
                ]);

                return [
                    'response' => $cachedResponse,
                    'cached' => true,
                    'processing_time' => (microtime(true) - $startTime) * 1000,
                ];
            }

            // Call AI service with timeout
            $response = $this->callAIWithTimeout($message, $context);

            $processingTime = (microtime(true) - $startTime) * 1000;

            // Cache successful responses for 1 hour
            if ($response) {
                Cache::put($cacheKey, $response, 3600);
            }

            Log::info('Chat response generated successfully', [
                'processing_time_ms' => $processingTime,
                'context' => $context,
                'cached' => false,
            ]);

            return [
                'response' => $response,
                'cached' => false,
                'processing_time' => $processingTime,
            ];

        } catch (Exception $e) {
            Log::error('Chat AI processing failed, using fallback', [
                'error' => $e->getMessage(),
                'context' => $context,
            ]);

            // Use fallback response (Reliability requirement)
            return [
                'response' => $this->getFallbackResponse($context),
                'cached' => false,
                'processing_time' => (microtime(true) - $startTime) * 1000,
                'fallback' => true,
            ];
        }
    }

    /**
     * Call AI service with timeout protection
     */
    protected function callAIWithTimeout(string $message, string $context): string
    {
        $systemPrompt = $this->getSystemPrompt($context);
        $fullPrompt = "{$systemPrompt}\n\nالمستخدم: {$message}";

        try {
            // Use Ollama service with timeout handling
            $response = $this->ollamaService->chatWithAI($fullPrompt);

            if (empty($response)) {
                throw new Exception('Empty response from AI');
            }

            return $response;

        } catch (Exception $e) {
            Log::warning('AI call failed, will use fallback', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get system prompt based on context
     */
    protected function getSystemPrompt(string $context): string
    {
        $prompts = [
            'business_plan' => 'أنت مساعد ذكاء اصطناعي متخصص في إنشاء وتحليل خطط العمل. قدم إجابات موجزة ومفيدة في 2-3 فقرات قصيرة.',
            'financial' => 'أنت مساعد ذكاء اصطناعي متخصص في التحليل المالي. قدم نصائح مالية دقيقة وموجزة في 2-3 فقرات.',
            'marketing' => 'أنت مساعد ذكاء اصطناعي متخصص في التسويق. قدم استراتيجيات تسويقية مختصرة وعملية.',
            'general' => 'أنت مساعد ذكاء اصطناعي مفيد. أجب بشكل موجز واحترافي في 2-3 فقرات.',
        ];

        return $prompts[$context] ?? $prompts['general'];
    }

    /**
     * Get cache key for message
     */
    protected function getCacheKey(string $message, string $context): string
    {
        return 'chat_ai:' . $context . ':' . md5($message);
    }

    /**
     * Load fallback responses for reliability
     */
    protected function loadFallbackResponses(): array
    {
        return [
            'business_plan' => 'شكراً لسؤالك. يمكنني مساعدتك في تطوير خطة عملك. خطة العمل الجيدة تتضمن: ملخص تنفيذي واضح، تحليل سوق شامل، استراتيجية تسويقية محددة، وخطة مالية واقعية. ما الجزء المحدد الذي تحتاج مساعدة فيه؟',

            'financial' => 'أستطيع مساعدتك في التحليل المالي. من المهم التركيز على: التدفقات النقدية، نسب الربحية، نقطة التعادل، والتوقعات المالية. ما التحليل المالي المحدد الذي تحتاجه؟',

            'marketing' => 'يسعدني مساعدتك في استراتيجية التسويق. استراتيجية التسويق الناجحة تشمل: تحديد الجمهور المستهدف، القيمة المميزة، القنوات التسويقية المناسبة، والميزانية. ما جانب التسويق الذي تريد التركيز عليه؟',

            'general' => 'أنا هنا لمساعدتك! يمكنني المساعدة في خطط الأعمال، التحليل المالي، استراتيجيات التسويق، وغيرها. كيف يمكنني مساعدتك اليوم؟',
        ];
    }

    /**
     * Get fallback response based on context
     */
    protected function getFallbackResponse(string $context): string
    {
        return $this->fallbackResponses[$context]
            ?? $this->fallbackResponses['general'];
    }

    /**
     * Get suggested questions for better usability
     */
    public function getSuggestedQuestions(string $context = 'general'): array
    {
        $suggestions = [
            'business_plan' => [
                'كيف أكتب ملخص تنفيذي قوي؟',
                'ما هي العناصر الأساسية في خطة العمل؟',
                'كيف أحدد السوق المستهدف؟',
                'ما الفرق بين الرؤية والرسالة؟',
            ],
            'financial' => [
                'كيف أحسب نقطة التعادل؟',
                'ما هي النسب المالية المهمة؟',
                'كيف أعد قائمة التدفقات النقدية؟',
                'كيف أحدد التكاليف الثابتة والمتغيرة؟',
            ],
            'marketing' => [
                'ما هي أفضل استراتيجيات التسويق الرقمي؟',
                'كيف أحدد القيمة المميزة لمنتجي؟',
                'ما القنوات التسويقية الأنسب للبدء؟',
                'كيف أقيس نجاح الحملات التسويقية؟',
            ],
            'general' => [
                'كيف أبدأ مشروعي الناشئ؟',
                'ما المصادر التمويلية المتاحة؟',
                'كيف أدير فريق العمل؟',
                'كيف أتعامل مع المنافسة؟',
            ],
        ];

        return $suggestions[$context] ?? $suggestions['general'];
    }

    /**
     * Check if user has given consent to store chats (Security requirement)
     */
    public function hasStorageConsent(int $userId): bool
    {
        return Cache::get("chat_storage_consent:user:{$userId}", false);
    }

    /**
     * Set user's chat storage consent
     */
    public function setStorageConsent(int $userId, bool $consent): void
    {
        if ($consent) {
            Cache::put("chat_storage_consent:user:{$userId}", true, 86400 * 365); // 1 year
        } else {
            Cache::forget("chat_storage_consent:user:{$userId}");
        }

        Log::info('Chat storage consent updated', [
            'user_id' => $userId,
            'consent' => $consent,
        ]);
    }

    /**
     * Get active chat sessions count (for monitoring scalability)
     */
    public function getActiveChatSessions(): int
    {
        return (int) Cache::get('active_chat_sessions_count', 0);
    }

    /**
     * Increment active chat sessions
     */
    public function incrementActiveSessions(): void
    {
        $count = $this->getActiveChatSessions();
        Cache::put('active_chat_sessions_count', $count + 1, 300); // 5 minutes TTL
    }

    /**
     * Decrement active chat sessions
     */
    public function decrementActiveSessions(): void
    {
        $count = $this->getActiveChatSessions();
        if ($count > 0) {
            Cache::put('active_chat_sessions_count', $count - 1, 300);
        }
    }
}
