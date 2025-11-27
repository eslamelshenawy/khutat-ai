<x-layouts.app>
    <x-slot name="title">المحادثة مع الذكاء الاصطناعي</x-slot>
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8 h-screen flex flex-col">
    <!-- Page Header -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">المحادثة مع الذكاء الاصطناعي</h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-1 sm:mt-2">احصل على مساعدة فورية في خطط العمل والتسويق والتحليل المالي</p>
    </div>

    <!-- Storage Consent Banner -->
    @if(!$hasStorageConsent)
    <div id="consent-banner" class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-sm sm:text-base font-bold text-blue-900 dark:text-blue-100 mb-2">🔒 حماية خصوصيتك</h3>
                <p class="text-xs sm:text-sm text-blue-800 dark:text-blue-200 mb-3">
                    هل توافق على حفظ محادثاتك؟ هذا يساعدنا على تحسين تجربتك وتقديم اقتراحات أفضل.
                    يمكنك تغيير هذا الإعداد في أي وقت.
                </p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button onclick="setConsent(true)" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs sm:text-sm">
                        موافق، احفظ المحادثات
                    </button>
                    <button onclick="setConsent(false)" class="px-3 sm:px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition text-xs sm:text-sm">
                        لا، استمر بدون حفظ
                    </button>
                </div>
            </div>
            <button onclick="document.getElementById('consent-banner').remove()" class="text-blue-600 dark:text-blue-300 hover:text-blue-800 mr-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Context Selector -->
    <div class="mb-3 sm:mb-4 flex gap-1.5 sm:gap-2 flex-wrap">
        <button type="button" class="context-btn px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg transition active text-xs sm:text-sm" data-context="general">
            💬 عام
        </button>
        <button type="button" class="context-btn px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg transition text-xs sm:text-sm" data-context="business_plan">
            📊 خطة العمل
        </button>
        <button type="button" class="context-btn px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg transition text-xs sm:text-sm" data-context="financial">
            💰 تحليل مالي
        </button>
        <button type="button" class="context-btn px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg transition text-xs sm:text-sm" data-context="marketing">
            📢 تسويق
        </button>
    </div>

    <!-- Suggested Questions -->
    <div id="suggested-questions" class="mb-3 sm:mb-4 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900 dark:to-blue-900 rounded-lg p-3 sm:p-4">
        <h3 class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 sm:mb-3">💡 أسئلة مقترحة:</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5 sm:gap-2">
            @foreach($suggestedQuestions as $question)
            <button onclick="sendSuggestedQuestion('{{ $question }}')"
                    class="text-right px-3 sm:px-4 py-2 bg-white dark:bg-gray-800 text-xs sm:text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900 transition border border-gray-200 dark:border-gray-700">
                {{ $question }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Chat Messages Container -->
    <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow mb-3 sm:mb-4 overflow-hidden flex flex-col">
        <div id="chat-messages" class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 space-y-3 sm:space-y-4">
            @foreach($messages as $message)
            <div class="message {{ $message->is_user ? 'user-message' : 'ai-message' }}">
                <div class="flex items-start gap-2 sm:gap-3 {{ $message->is_user ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        @if($message->is_user)
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 max-w-full sm:max-w-[85%]">
                        <div class="message-bubble {{ $message->is_user ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-lg p-2.5 sm:p-3 md:p-4">
                            <p class="text-xs sm:text-sm whitespace-pre-wrap break-words">{{ $message->message }}</p>
                        </div>
                        <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->is_user ? 'text-left' : 'text-right' }}">
                            {{ $message->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Loading Indicator -->
        <div id="loading-indicator" class="hidden px-3 sm:px-6 py-2 sm:py-3">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">جاري التفكير...</span>
                    <div id="performance-indicator" class="text-[10px] sm:text-xs text-gray-500"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Input -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-2.5 sm:p-3 md:p-4">
        <form id="chat-form" class="flex gap-2 sm:gap-3">
            @csrf
            <input type="hidden" id="context-input" name="context" value="general">
            <input
                type="text"
                id="message-input"
                name="message"
                placeholder="اكتب رسالتك هنا..."
                class="flex-1 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                required
                maxlength="2000"
            >
            <button
                type="submit"
                id="send-button"
                class="px-3 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1 sm:gap-2"
            >
                <span class="hidden sm:inline">إرسال</span>
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
        <div id="error-message" class="hidden mt-2 text-red-600 dark:text-red-400 text-xs sm:text-sm"></div>
        <div id="performance-info" class="hidden mt-2 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400"></div>
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
    const performanceInfo = document.getElementById('performance-info');
    const performanceIndicator = document.getElementById('performance-indicator');
    const contextButtons = document.querySelectorAll('.context-btn');

    let requestStartTime = 0;

    // Auto-scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    scrollToBottom();

    // Context button handling
    contextButtons.forEach(button => {
        button.addEventListener('click', function() {
            contextButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            contextInput.value = this.dataset.context;

            // Update suggested questions
            updateSuggestedQuestions(this.dataset.context);
        });
    });

    // Update suggested questions
    async function updateSuggestedQuestions(context) {
        try {
            const response = await fetch(`{{ route('chat.suggested-questions') }}?context=${context}`);
            const data = await response.json();

            if (data.success) {
                const container = document.getElementById('suggested-questions');
                const grid = container.querySelector('.grid');
                grid.innerHTML = '';

                data.questions.forEach(question => {
                    const button = document.createElement('button');
                    button.className = 'text-right px-3 sm:px-4 py-2 bg-white dark:bg-gray-800 text-xs sm:text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900 transition border border-gray-200 dark:border-gray-700';
                    button.textContent = question;
                    button.onclick = () => sendSuggestedQuestion(question);
                    grid.appendChild(button);
                });
            }
        } catch (error) {
            console.error('Failed to update suggested questions:', error);
        }
    }

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
            <div class="flex items-start gap-2 sm:gap-3 ${alignment}">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full ${bgColor} flex items-center justify-center">
                        ${iconSvg}
                    </div>
                </div>
                <div class="flex-1 max-w-full sm:max-w-[85%]">
                    <div class="message-bubble ${bubbleColor} rounded-lg p-2.5 sm:p-3 md:p-4">
                        <p class="text-xs sm:text-sm whitespace-pre-wrap break-words">${escapeHtml(content)}</p>
                    </div>
                    <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-1 ${timeAlignment}">
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

    // Send message
    async function sendMessage(message) {
        if (!message.trim()) return;

        // Disable input
        messageInput.disabled = true;
        sendButton.disabled = true;
        errorMessage.classList.add('hidden');
        performanceInfo.classList.add('hidden');

        // Add user message
        addMessage('user', message);
        messageInput.value = '';

        // Show loading and start timer
        loadingIndicator.classList.remove('hidden');
        requestStartTime = Date.now();

        // Update performance indicator every 100ms
        const performanceInterval = setInterval(() => {
            const elapsed = ((Date.now() - requestStartTime) / 1000).toFixed(1);
            performanceIndicator.textContent = `(${elapsed}s)`;
        }, 100);

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
            clearInterval(performanceInterval);

            if (data.success) {
                // Add AI message
                addMessage('assistant', data.ai_message.message);

                // Show performance info
                const perfTime = (data.processing_time / 1000).toFixed(2);
                const perfStatus = data.processing_time <= 2000 ? '✅' : '⚠️';
                const cacheStatus = data.cached ? '💾 من الذاكرة' : '🔥 جديد';
                performanceInfo.textContent = `${perfStatus} زمن الاستجابة: ${perfTime}s | ${cacheStatus}`;
                performanceInfo.classList.remove('hidden');

                if (data.storage_disabled) {
                    performanceInfo.textContent += ' | 🔒 لم يتم حفظ المحادثة';
                }
            } else {
                throw new Error(data.error || 'حدث خطأ غير متوقع');
            }
        } catch (error) {
            clearInterval(performanceInterval);
            console.error('Chat error:', error);
            errorMessage.textContent = error.message || 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.';
            errorMessage.classList.remove('hidden');
        } finally {
            // Hide loading and enable input
            loadingIndicator.classList.add('hidden');
            performanceIndicator.textContent = '';
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        }
    }

    // Form submission
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        await sendMessage(messageInput.value.trim());
    });

    // Send suggested question
    window.sendSuggestedQuestion = async function(question) {
        messageInput.value = question;
        await sendMessage(question);
    };

    // Set storage consent
    window.setConsent = async function(consent) {
        try {
            const response = await fetch('{{ route('chat.storage-consent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ consent: consent })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('consent-banner').remove();

                // Show notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg z-50 text-xs sm:text-sm max-w-[90%] sm:max-w-md text-center';
                notification.textContent = data.message;
                document.body.appendChild(notification);

                setTimeout(() => notification.remove(), 3000);
            }
        } catch (error) {
            console.error('Failed to set consent:', error);
        }
    };
});
</script>
</x-layouts.app>
