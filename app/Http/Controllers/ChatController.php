<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
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

        return view('chat.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'context' => 'nullable|string',
        ]);

        $userMessage = $request->input('message');
        $context = $request->input('context', 'general');

        // Get the latest business plan or create a general chat plan
        $businessPlan = auth()->user()->businessPlans()->latest()->first();
        if (!$businessPlan) {
            // Create a general chat business plan if none exists
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

        try {
            // Generate AI response
            $aiResponse = $this->generateAIResponse($userMessage, $context);

            // Save AI response
            $aiChatMessage = auth()->user()->chatMessages()->create([
                'business_plan_id' => $businessPlan->id,
                'message' => $aiResponse,
                'is_user' => false,
                'context' => ['type' => $context, 'parent_message_id' => $userChatMessage->id],
                'ai_model' => 'ollama',
            ]);

            return response()->json([
                'success' => true,
                'user_message' => $userChatMessage,
                'ai_message' => $aiChatMessage,
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

    protected function generateAIResponse(string $userMessage, string $context): string
    {
        $systemPrompt = $this->getSystemPrompt($context);

        $prompt = "{$systemPrompt}\n\nالمستخدم: {$userMessage}";

        // Use Ollama service to generate response
        try {
            $response = $this->ollamaService->chatWithAI($prompt);
            return $response ?: 'عذراً، لم أتمكن من توليد رد مناسب. يرجى المحاولة مرة أخرى.';
        } catch (\Exception $e) {
            \Log::error('Chat AI generation failed', ['error' => $e->getMessage()]);
            return 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي. يرجى المحاولة مرة أخرى لاحقاً.';
        }
    }

    protected function getSystemPrompt(string $context): string
    {
        return match($context) {
            'business_plan' => 'أنت مساعد ذكاء اصطناعي متخصص في إنشاء وتحليل خطط العمل. مهمتك مساعدة المستخدمين في كتابة خطط عمل احترافية وتقديم النصائح والتوجيهات.',
            'financial' => 'أنت مساعد ذكاء اصطناعي متخصص في التحليل المالي والتخطيط المالي للمشاريع. قدم نصائح مالية دقيقة ومفيدة.',
            'marketing' => 'أنت مساعد ذكاء اصطناعي متخصص في التسويق واستراتيجيات الأعمال. ساعد المستخدم في تطوير استراتيجيات تسويقية فعالة.',
            default => 'أنت مساعد ذكاء اصطناعي مفيد ومحترف. ساعد المستخدم بأفضل ما لديك من معلومات ونصائح.',
        };
    }
}
