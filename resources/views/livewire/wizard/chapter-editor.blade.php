<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Chapters List -->
        <div class="w-80 bg-white shadow-xl border-l border-gray-200 overflow-y-auto flex-shrink-0">
            <div class="sticky top-0 z-10 bg-gradient-to-l from-indigo-600 to-indigo-700 px-6 py-5 shadow-md">
                <h2 class="text-xl font-bold text-white">{{ $businessPlan->title }}</h2>
                <p class="text-sm text-indigo-100 mt-1 flex items-center">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    محرر الفصول المتقدم
                </p>
            </div>

            <div class="p-4">
                <div class="mb-4 p-3 bg-gradient-to-l from-blue-50 to-indigo-50 rounded-lg border border-indigo-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700 font-medium">التقدم الكلي</span>
                        <span class="text-indigo-600 font-bold">{{ $businessPlan->completion_percentage }}%</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-l from-indigo-500 to-indigo-600 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $businessPlan->completion_percentage }}%"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3 px-1">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">قائمة الفصول</h3>
                    <button
                        id="toggleDragMode"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1"
                        title="تفعيل/إلغاء إعادة الترتيب"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        <span id="dragModeText">إعادة ترتيب</span>
                    </button>
                </div>

                <div id="chaptersList" class="space-y-2">
                    @forelse($chapters as $index => $chapter)
                        <button
                            data-chapter-id="{{ $chapter->id }}"
                            wire:click="selectChapter({{ $chapter->id }})"
                            class="chapter-item w-full text-right px-4 py-3.5 rounded-lg transition-all duration-200 group
                                   {{ $currentChapter && $currentChapter->id == $chapter->id
                                      ? 'bg-gradient-to-l from-indigo-100 to-indigo-50 border-r-4 border-indigo-600 shadow-sm'
                                      : 'hover:bg-gray-50 border border-transparent hover:border-gray-200' }}"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2 drag-handle cursor-move opacity-0 transition-opacity">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-right">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                            {{ $currentChapter && $currentChapter->id == $chapter->id
                                               ? 'bg-indigo-600 text-white'
                                               : 'bg-gray-200 text-gray-600 group-hover:bg-gray-300' }}">
                                            {{ $chapter->chapter_number ?? ($index + 1) }}
                                        </span>
                                        <span class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $chapter->title }}</span>
                                    </div>

                                    <div class="flex items-center gap-2 mt-2">
                                        @if($chapter->is_ai_generated)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                                <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 7H7v6h6V7z"/>
                                                    <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                                </svg>
                                                AI
                                            </span>
                                        @endif

                                        @if($chapter->word_count > 0)
                                            <span class="text-xs text-gray-500">{{ $chapter->word_count }} كلمة</span>
                                        @else
                                            <span class="text-xs text-gray-400">فارغ</span>
                                        @endif
                                    </div>
                                </div>

                                @if($currentChapter && $currentChapter->id == $chapter->id)
                                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">لا توجد فصول</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('business-plans.show', $businessPlan) }}"
                   class="mt-6 w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    رجوع للخطة
                </a>
            </div>
        </div>

        <!-- Main Editor Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white">
            <!-- Top Action Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        @if($currentChapter)
                            <h3 class="text-2xl font-bold text-gray-900">{{ $currentChapter->title }}</h3>
                            <div class="flex items-center gap-4 mt-1.5">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    عدد الكلمات: <span class="font-semibold mr-1">{{ $currentChapter->word_count ?? 0 }}</span>
                                </p>
                                @if($currentChapter->is_ai_generated)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 7H7v6h6V7z"/>
                                            <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                        </svg>
                                        تم التوليد بالذكاء الاصطناعي
                                    </span>
                                @endif
                            </div>
                        @else
                            <h3 class="text-2xl font-bold text-gray-400">اختر فصلاً للبدء</h3>
                            <p class="text-sm text-gray-500 mt-1">اختر فصلاً من القائمة الجانبية لتحريره</p>
                        @endif
                    </div>

                    @if($currentChapter)
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="saveChapter"
                                wire:loading.attr="disabled"
                                wire:target="saveChapter"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="saveChapter" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                <svg wire:loading wire:target="saveChapter" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="saveChapter">حفظ</span>
                                <span wire:loading wire:target="saveChapter">جاري الحفظ...</span>
                            </button>

                            <button
                                wire:click="generateWithAI"
                                wire:loading.attr="disabled"
                                wire:target="generateWithAI"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="generateWithAI" class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 7H7v6h6V7z"/>
                                    <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                </svg>
                                <svg wire:loading wire:target="generateWithAI" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="generateWithAI">توليد بالذكاء الاصطناعي</span>
                                <span wire:loading wire:target="generateWithAI">جاري التوليد...</span>
                            </button>

                            <button
                                wire:click="improveContent"
                                wire:loading.attr="disabled"
                                wire:target="improveContent"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="improveContent" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <svg wire:loading wire:target="improveContent" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="improveContent">تحسين المحتوى</span>
                                <span wire:loading wire:target="improveContent">جاري التحسين...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Editor -->
            <div class="flex-1 overflow-y-auto p-8 bg-gray-50">
                @if($currentChapter)
                    <div class="max-w-5xl mx-auto">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <textarea
                                wire:model="content"
                                rows="24"
                                class="w-full px-6 py-5 border-0 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 font-['Tajawal'] text-base leading-relaxed resize-none"
                                style="min-height: calc(100vh - 400px);"
                                placeholder="اكتب محتوى الفصل هنا بالتفصيل...&#10;&#10;يمكنك استخدام الذكاء الاصطناعي لتوليد محتوى احترافي أو تحسين المحتوى الموجود.&#10;&#10;نصائح:&#10;• اكتب بوضوح وبساطة&#10;• استخدم فقرات قصيرة&#10;• أضف أمثلة عملية&#10;• راجع المحتوى قبل الحفظ"></textarea>
                        </div>

                        @if(!empty($content))
                            <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-700">
                                            <span class="font-semibold">{{ str_word_count(strip_tags($content)) }}</span> كلمة
                                        </span>
                                        <span class="text-gray-400">|</span>
                                        <span class="text-gray-700">
                                            <span class="font-semibold">{{ mb_strlen(strip_tags($content)) }}</span> حرف
                                        </span>
                                        <span class="text-gray-400">|</span>
                                        <span class="text-gray-700">
                                            وقت القراءة: <span class="font-semibold">{{ ceil(str_word_count(strip_tags($content)) / 200) }}</span> دقيقة
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">آخر تعديل: {{ $currentChapter->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="mx-auto h-20 w-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <h3 class="mt-4 text-xl font-medium text-gray-900">لم يتم اختيار فصل</h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                                اختر فصلاً من القائمة الجانبية للبدء في الكتابة والتحرير
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- AI Chat Panel -->
            @if($currentChapter)
                <div class="bg-white border-t border-gray-200 shadow-lg flex-shrink-0">
                    <div class="px-6 py-3 bg-gradient-to-l from-indigo-50 to-purple-50 border-b border-indigo-100 flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            مساعد الذكاء الاصطناعي
                        </h4>
                        <span class="text-xs text-gray-500">اطرح أسئلة أو اطلب اقتراحات</span>
                    </div>

                    <div class="px-6 py-4">
                        @if(count($chatMessages) > 0)
                            <div class="max-h-48 overflow-y-auto mb-4 space-y-3 p-4 bg-gray-50 rounded-lg">
                                @foreach($chatMessages as $message)
                                    <div class="flex {{ $message['role'] == 'user' ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[80%] px-4 py-2.5 rounded-lg shadow-sm
                                            {{ $message['role'] == 'user'
                                               ? 'bg-gradient-to-l from-indigo-600 to-indigo-700 text-white'
                                               : 'bg-white border border-gray-200 text-gray-800' }}">
                                            <p class="text-sm leading-relaxed">{{ $message['content'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex gap-3">
                            <input
                                wire:model="chatInput"
                                wire:keydown.enter="sendChatMessage"
                                type="text"
                                placeholder="اطرح سؤالاً أو اطلب اقتراحات لتحسين الفصل..."
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >
                            <button
                                wire:click="sendChatMessage"
                                wire:loading.attr="disabled"
                                wire:target="sendChatMessage"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 shadow-sm transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="sendChatMessage" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <svg wire:loading wire:target="sendChatMessage" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="sendChatMessage">إرسال</span>
                                <span wire:loading wire:target="sendChatMessage">إرسال...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bottom Action Bar -->
            @if($currentChapter)
                <div class="bg-gradient-to-l from-gray-50 to-white border-t border-gray-200 px-6 py-4 flex justify-between items-center shadow-inner flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">آخر حفظ:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $currentChapter->updated_at->diffForHumans() }}</span>
                    </div>
                    <button
                        wire:click="finishEditing"
                        class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-sm font-bold text-white bg-gradient-to-l from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transition-all transform hover:scale-105"
                    >
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        حفظ وإنهاء التحرير
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Notification Toast --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-lg shadow-xl border-2 px-6 py-4 max-w-md"
             :class="{ 'border-green-500': type === 'success', 'border-red-500': type === 'error', 'border-blue-500': type === 'info' }">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="type === 'error'" class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="type === 'info'" class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sortable.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- Drag & Drop Functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chaptersList = document.getElementById('chaptersList');
            const toggleButton = document.getElementById('toggleDragMode');
            const dragModeText = document.getElementById('dragModeText');
            let sortable = null;
            let isDragMode = false;

            // Initialize Sortable (disabled by default)
            sortable = new Sortable(chaptersList, {
                animation: 150,
                handle: '.drag-handle',
                disabled: true,
                ghostClass: 'bg-indigo-100',
                chosenClass: 'ring-2 ring-indigo-500',
                dragClass: 'opacity-50',
                onEnd: function(evt) {
                    saveNewOrder();
                }
            });

            // Toggle drag mode
            toggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                isDragMode = !isDragMode;
                sortable.option('disabled', !isDragMode);

                const dragHandles = document.querySelectorAll('.drag-handle');
                const chapterItems = document.querySelectorAll('.chapter-item');

                if (isDragMode) {
                    dragModeText.textContent = 'حفظ الترتيب';
                    toggleButton.classList.add('text-green-600', 'hover:text-green-800');
                    toggleButton.classList.remove('text-indigo-600', 'hover:text-indigo-800');
                    dragHandles.forEach(handle => handle.classList.remove('opacity-0'));
                    chapterItems.forEach(item => {
                        item.style.pointerEvents = 'auto';
                        item.removeAttribute('wire:click');
                    });
                } else {
                    dragModeText.textContent = 'إعادة ترتيب';
                    toggleButton.classList.remove('text-green-600', 'hover:text-green-800');
                    toggleButton.classList.add('text-indigo-600', 'hover:text-indigo-800');
                    dragHandles.forEach(handle => handle.classList.add('opacity-0'));
                    saveNewOrder();
                }
            });

            function saveNewOrder() {
                const chapterItems = document.querySelectorAll('.chapter-item');
                const chapters = [];

                chapterItems.forEach((item, index) => {
                    const chapterId = item.getAttribute('data-chapter-id');
                    if (chapterId) {
                        chapters.push({
                            id: parseInt(chapterId),
                            sort_order: index
                        });
                    }
                });

                if (chapters.length > 0) {
                    fetch('{{ route("business-plans.chapters.reorder", $businessPlan) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ chapters: chapters })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    message: data.message || 'تم تحديث ترتيب الفصول بنجاح',
                                    type: 'success'
                                }
                            }));

                            // Reload the page to reflect new order
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'فشل تحديث الترتيب');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                message: error.message || 'حدث خطأ أثناء حفظ الترتيب',
                                type: 'error'
                            }
                        }));
                    });
                }
            }
        });
    </script>
</div>
