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

        // Save user message
        $userChatMessage = auth()->user()->chatMessages()->create([
            'role' => 'user',
            'content' => $userMessage,
            'context' => $context,
        ]);

        try {
            // Generate AI response
            $aiResponse = $this->generateAIResponse($userMessage, $context);

            // Save AI response
            $aiChatMessage = auth()->user()->chatMessages()->create([
                'role' => 'assistant',
                'content' => $aiResponse,
                'context' => $context,
                'parent_message_id' => $userChatMessage->id,
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

        $prompt = "{$systemPrompt}\n\nالمستخدم: {$userMessage}\n\nالمساعد:";

        // Use Ollama service to generate response
        $response = $this->ollamaService->generateText($prompt, [
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ]);

        return $response ?? 'عذراً، لم أتمكن من توليد رد مناسب. يرجى المحاولة مرة أخرى.';
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
