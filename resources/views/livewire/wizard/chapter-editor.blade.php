<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="flex h-screen">
        <!-- Sidebar - Chapters List -->
        <div class="w-64 bg-white shadow-lg overflow-y-auto">
            <div class="p-4 bg-indigo-600 text-white">
                <h2 class="text-xl font-bold">{{ $businessPlan->title }}</h2>
                <p class="text-sm opacity-90 mt-1">تحرير الفصول</p>
            </div>

            <div class="p-4">
                @foreach($chapters as $chapter)
                    <div wire:click="selectChapter({{ $chapter->id }})"
                         class="p-3 mb-2 rounded cursor-pointer transition-colors
                                {{ $currentChapter && $currentChapter->id == $chapter->id ? 'bg-indigo-100 border-r-4 border-indigo-600' : 'hover:bg-gray-100' }}">
                        <p class="font-medium text-sm">{{ $chapter->chapter_number }}. {{ $chapter->title }}</p>
                        @if($chapter->is_ai_generated)
                            <span class="text-xs text-indigo-600">✨ AI</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Editor Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white shadow px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">{{ $currentChapter ? $currentChapter->title : 'اختر فصلاً' }}</h3>
                    @if($currentChapter)
                        <p class="text-sm text-gray-600">عدد الكلمات: {{ $currentChapter->word_count ?? 0 }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button wire:click="saveChapter"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        حفظ
                    </button>
                    <button wire:click="generateWithAI"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="generateWithAI">✨ إنشاء بالذكاء الاصطناعي</span>
                        <span wire:loading wire:target="generateWithAI">جاري الإنشاء...</span>
                    </button>
                    <button wire:click="improveContent"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50">
                        تحسين المحتوى
                    </button>
                </div>
            </div>

            <!-- Content Editor -->
            <div class="flex-1 overflow-y-auto p-6">
                @if($currentChapter)
                    <textarea
                        wire:model="content"
                        rows="20"
                        class="w-full h-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 font-sans"
                        placeholder="اكتب محتوى الفصل هنا أو استخدم الذكاء الاصطناعي لإنشائه..."></textarea>
                @else
                    <div class="flex items-center justify-center h-full text-gray-400">
                        <p>اختر فصلاً من القائمة الجانبية للبدء</p>
                    </div>
                @endif
            </div>

            <!-- AI Chat Panel (Toggle) -->
            <div class="bg-white border-t p-4">
                <h4 class="font-bold mb-3">💬 مساعد الذكاء الاصطناعي</h4>
                <div class="max-h-40 overflow-y-auto mb-3 space-y-2">
                    @foreach($chatMessages as $message)
                        <div class="p-2 rounded {{ $message['role'] == 'user' ? 'bg-blue-50 text-right' : 'bg-gray-100 text-left' }}">
                            <p class="text-sm">{{ $message['content'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <input
                        wire:model="chatInput"
                        wire:keydown.enter="sendChatMessage"
                        type="text"
                        placeholder="اسأل الذكاء الاصطناعي..."
                        class="flex-1 px-3 py-2 border rounded focus:ring-2 focus:ring-indigo-500">
                    <button wire:click="sendChatMessage"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        إرسال
                    </button>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="bg-white border-t px-6 py-3 flex justify-between">
                <button class="text-gray-600 hover:text-gray-800">
                    حفظ كمسودة
                </button>
                <button wire:click="finishEditing"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    إنهاء ونشر الخطة
                </button>
            </div>
        </div>
    </div>
</div>
