<x-layouts.app>
    <x-slot name="title">المحادثة مع الذكاء الاصطناعي</x-slot>
<div class="container mx-auto px-4 py-8 h-screen flex flex-col">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">المحادثة مع الذكاء الاصطناعي</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">احصل على مساعدة فورية في خطط العمل والتسويق والتحليل المالي</p>
    </div>

    <!-- Context Selector -->
    <div class="mb-4 flex gap-2">
        <button type="button" class="context-btn px-4 py-2 rounded-lg transition" data-context="general">
            عام
        </button>
        <button type="button" class="context-btn px-4 py-2 rounded-lg transition" data-context="business_plan">
            خطة العمل
        </button>
        <button type="button" class="context-btn px-4 py-2 rounded-lg transition" data-context="financial">
            تحليل مالي
        </button>
        <button type="button" class="context-btn px-4 py-2 rounded-lg transition" data-context="marketing">
            تسويق
        </button>
    </div>

    <!-- Chat Messages Container -->
    <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow mb-4 overflow-hidden flex flex-col">
        <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4">
            @foreach($messages as $message)
            <div class="message {{ $message->is_user ? 'user-message' : 'ai-message' }}">
                <div class="flex items-start gap-3 {{ $message->is_user ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        @if($message->is_user)
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="message-bubble {{ $message->is_user ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-lg p-4">
                            <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->is_user ? 'text-left' : 'text-right' }}">
                            {{ $message->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden px-6 py-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <span class="text-gray-600 dark:text-gray-400">جاري التفكير...</span>
            </div>
        </div>
    </div>

    <!-- Message Input -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <form id="chat-form" class="flex gap-3">
            @csrf
            <input type="hidden" id="context-input" name="context" value="general">
            <input
                type="text"
                id="message-input"
                name="message"
                placeholder="اكتب رسالتك هنا..."
                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                required
                maxlength="2000"
            >
            <button
                type="submit"
                id="send-button"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
            >
                <span>إرسال</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
        <div id="error-message" class="hidden mt-2 text-red-600 dark:text-red-400 text-sm"></div>
    </div>
</div>

<style>
    .context-btn {
        background-color: #e5e7eb;
        color: #374151;
    }
    .context-btn.active {
        background-color: #3b82f6;
        color: white;
    }
    .dark .context-btn {
        background-color: #374151;
        color: #d1d5db;
    }
    .dark .context-btn.active {
        background-color: #3b82f6;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const contextInput = document.getElementById('context-input');
    const sendButton = document.getElementById('send-button');
    const chatMessages = document.getElementById('chat-messages');
    const loadingIndicator = document.getElementById('loading-indicator');
    const errorMessage = document.getElementById('error-message');
    const contextButtons = document.querySelectorAll('.context-btn');

    // Set initial active context
    contextButtons[0].classList.add('active');

    // Context button handling
    contextButtons.forEach(button => {
        button.addEventListener('click', function() {
            contextButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            contextInput.value = this.dataset.context;
        });
    });

    // Auto-scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    scrollToBottom();

    // Add message to UI
    function addMessage(role, content, timestamp = new Date()) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role === 'user' ? 'user-message' : 'ai-message'}`;

        const iconSvg = role === 'user'
            ? '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            : '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>';

        const bgColor = role === 'user' ? 'bg-blue-600' : 'bg-purple-600';
        const bubbleColor = role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white';
        const alignment = role === 'user' ? 'flex-row-reverse' : '';
        const timeAlignment = role === 'user' ? 'text-left' : 'text-right';

        messageDiv.innerHTML = `
            <div class="flex items-start gap-3 ${alignment}">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full ${bgColor} flex items-center justify-center">
                        ${iconSvg}
                    </div>
                </div>
                <div class="flex-1">
                    <div class="message-bubble ${bubbleColor} rounded-lg p-4">
                        <p class="text-sm whitespace-pre-wrap">${escapeHtml(content)}</p>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 ${timeAlignment}">
                        الآن
                    </div>
                </div>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Form submission
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input
        messageInput.disabled = true;
        sendButton.disabled = true;
        errorMessage.classList.add('hidden');

        // Add user message
        addMessage('user', message);
        messageInput.value = '';

        // Show loading
        loadingIndicator.classList.remove('hidden');

        try {
            const response = await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    context: contextInput.value
                })
            });

            const data = await response.json();

            if (data.success) {
                // Add AI message
                addMessage('assistant', data.ai_message.message);
            } else {
                throw new Error(data.error || 'حدث خطأ غير متوقع');
            }
        } catch (error) {
            console.error('Chat error:', error);
            errorMessage.textContent = error.message || 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.';
            errorMessage.classList.remove('hidden');
        } finally {
            // Hide loading and enable input
            loadingIndicator.classList.add('hidden');
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        }
    });
});
</script>
</x-layouts.app>
