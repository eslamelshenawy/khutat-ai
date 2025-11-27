<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\AI\ChatAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected ChatAIService $chatAIService;

    public function __construct(ChatAIService $chatAIService)
    {
        $this->chatAIService = $chatAIService;
    }

    public function index()
    {
        $messages = auth()->user()
            ->chatMessages()
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // Get suggested questions based on last context
        $lastMessage = $messages->where('is_user', false)->last();
        $lastContext = $lastMessage->context['type'] ?? 'general';
        $suggestedQuestions = $this->chatAIService->getSuggestedQuestions($lastContext);

        // Check storage consent
        $hasStorageConsent = $this->chatAIService->hasStorageConsent(auth()->id());

        return view('chat.index', compact('messages', 'suggestedQuestions', 'hasStorageConsent'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'context' => 'nullable|string',
        ]);

        $userMessage = $request->input('message');
        $context = $request->input('context', 'general');

        // Track active sessions for scalability monitoring
        $this->chatAIService->incrementActiveSessions();

        try {
            // Process message with ChatAIService (handles performance, fallback, caching)
            $result = $this->chatAIService->processMessage($userMessage, $context);
            $aiResponse = $result['response'];

            // Only save messages if user has given consent (Security requirement)
            $hasConsent = $this->chatAIService->hasStorageConsent(auth()->id());

            if ($hasConsent) {
                // Get the latest business plan or create a general chat plan
                $businessPlan = auth()->user()->businessPlans()->latest()->first();
                if (!$businessPlan) {
                    $businessPlan = auth()->user()->businessPlans()->create([
                        'title' => 'محادثات عامة',
                        'company_name' => 'محادثة مع AI',
                        'project_type' => 'general',
                        'industry_type' => 'general',
                        'status' => 'draft',
                    ]);
                }

                // Save user message
                $userChatMessage = auth()->user()->chatMessages()->create([
                    'business_plan_id' => $businessPlan->id,
                    'message' => $userMessage,
                    'is_user' => true,
                    'context' => ['type' => $context],
                ]);

                // Save AI response
                $aiChatMessage = auth()->user()->chatMessages()->create([
                    'business_plan_id' => $businessPlan->id,
                    'message' => $aiResponse,
                    'is_user' => false,
                    'context' => [
                        'type' => $context,
                        'parent_message_id' => $userChatMessage->id,
                        'cached' => $result['cached'] ?? false,
                        'fallback' => $result['fallback'] ?? false,
                        'processing_time_ms' => $result['processing_time'],
                    ],
                    'ai_model' => 'ollama',
                ]);
            } else {
                // Create temporary message objects for response only
                $userChatMessage = (object)['message' => $userMessage, 'created_at' => now()];
                $aiChatMessage = (object)['message' => $aiResponse, 'created_at' => now()];
            }

            return response()->json([
                'success' => true,
                'user_message' => $userChatMessage,
                'ai_message' => $aiChatMessage,
                'processing_time' => $result['processing_time'],
                'cached' => $result['cached'] ?? false,
                'storage_disabled' => !$hasConsent,
            ]);

        } catch (\Exception $e) {
            Log::error('Chat AI Error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'عذراً، حدث خطأ أثناء الاتصال بالذكاء الاصطناعي. يرجى المحاولة مرة أخرى.',
            ], 500);
        } finally {
            // Decrement active sessions
            $this->chatAIService->decrementActiveSessions();
        }
    }

    public function history()
    {
        $messages = auth()->user()
            ->chatMessages()
            ->with('parentMessage')
            ->latest()
            ->paginate(30);

        return response()->json($messages);
    }

    /**
     * Set user's storage consent
     */
    public function setStorageConsent(Request $request)
    {
        $request->validate([
            'consent' => 'required|boolean',
        ]);

        $this->chatAIService->setStorageConsent(
            auth()->id(),
            $request->boolean('consent')
        );

        return response()->json([
            'success' => true,
            'message' => $request->boolean('consent')
                ? 'تم حفظ موافقتك على تخزين المحادثات'
                : 'تم إلغاء تخزين المحادثات',
        ]);
    }

    /**
     * Get suggested questions
     */
    public function suggestedQuestions(Request $request)
    {
        $context = $request->input('context', 'general');
        $questions = $this->chatAIService->getSuggestedQuestions($context);

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }
}
