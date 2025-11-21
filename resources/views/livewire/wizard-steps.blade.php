<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $plan->title }}</h1>
                    <p class="text-gray-600 mt-1">معالج إنشاء خطة العمل</p>
                </div>
                <a href="{{ route('business-plans.show', $plan) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    رجوع
                </a>
            </div>

            {{-- Progress Bar --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">نسبة الإنجاز</span>
                    <span class="text-sm font-bold text-blue-600">{{ $plan->completion_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-l from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500"
                         style="width: {{ $plan->completion_percentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $chapters->where('content', '!=', '')->count() }} من {{ $chapters->count() }} فصول مكتملة
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Chapters Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-l from-blue-600 to-blue-700 px-4 py-3">
                        <h2 class="text-white font-semibold text-lg">الفصول</h2>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto">
                        @forelse($chapters as $index => $chapter)
                            <button
                                wire:click="selectChapter({{ $chapter->id }})"
                                class="w-full text-right px-4 py-4 hover:bg-gray-50 transition-colors {{ $currentChapterId == $chapter->id ? 'bg-blue-50 border-r-4 border-blue-600' : '' }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium
                                                {{ $currentChapterId == $chapter->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $chapter->title }}</span>
                                        </div>

                                        {{-- Chapter Status Badge --}}
                                        <div class="mt-2">
                                            @if(!empty($chapter->content))
                                                @if($chapter->is_ai_generated)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M13 7H7v6h6V7z"/>
                                                            <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                                        </svg>
                                                        AI
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        مكتمل
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    فارغ
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($currentChapterId == $chapter->id)
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm">لا توجد فصول</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Main Content Area --}}
            <div class="lg:col-span-3">
                @if($currentChapter)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        {{-- Chapter Header --}}
                        <div class="border-b border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ $currentChapter['title'] }}</h2>
                                    @if($currentChapter['description'])
                                        <p class="text-sm text-gray-600 mt-1">{{ $currentChapter['description'] }}</p>
                                    @endif
                                </div>

                                {{-- AI Generate Button --}}
                                <button
                                    wire:click="generateWithAI"
                                    wire:loading.attr="disabled"
                                    wire:target="generateWithAI"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                                >
                                    <svg wire:loading.remove wire:target="generateWithAI" class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 7H7v6h6V7z"/>
                                        <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg wire:loading wire:target="generateWithAI" class="animate-spin -ml-1 ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="generateWithAI">توليد بالذكاء الاصطناعي</span>
                                    <span wire:loading wire:target="generateWithAI">جاري التوليد...</span>
                                </button>
                            </div>
                        </div>

                        {{-- Content Editor --}}
                        <div class="px-6 py-6">
                            <div class="mb-4" wire:key="chapter-{{ $currentChapterId }}-{{ $refreshKey }}">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    محتوى الفصل
                                </label>
                                <textarea
                                    wire:model.live="currentChapter.content"
                                    id="content"
                                    rows="16"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none font-['Tajawal']"
                                    placeholder="اكتب محتوى هذا الفصل هنا، أو استخدم الذكاء الاصطناعي لتوليد المحتوى تلقائياً..."
                                ></textarea>

                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>
                                        @if(!empty($currentChapter['content']))
                                            عدد الكلمات: {{ str_word_count(strip_tags($currentChapter['content'])) }}
                                        @else
                                            لم يتم كتابة محتوى بعد
                                        @endif
                                    </span>
                                    @if($currentChapter['is_ai_generated'])
                                        <span class="text-purple-600 font-medium">
                                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 7H7v6h6V7z"/>
                                                <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                            </svg>
                                            تم التوليد بواسطة AI
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
                                <div class="flex gap-3">
                                    {{-- Previous Button --}}
                                    <button
                                        wire:click="previousChapter"
                                        @if($chapters->first()->id == $currentChapterId) disabled @endif
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        السابق
                                    </button>

                                    {{-- Next Button --}}
                                    <button
                                        wire:click="nextChapter"
                                        @if($chapters->last()->id == $currentChapterId) disabled @endif
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        التالي
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex gap-3">
                                    {{-- Save Button --}}
                                    <button
                                        wire:click="saveChapter"
                                        wire:loading.attr="disabled"
                                        wire:target="saveChapter"
                                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all"
                                    >
                                        <svg wire:loading.remove wire:target="saveChapter" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                        </svg>
                                        <svg wire:loading wire:target="saveChapter" class="animate-spin -ml-1 ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="saveChapter">حفظ</span>
                                        <span wire:loading wire:target="saveChapter">جاري الحفظ...</span>
                                    </button>

                                    {{-- Complete Plan Button (only on last chapter) --}}
                                    @if($chapters->last()->id == $currentChapterId && $plan->completion_percentage >= 80)
                                        <button
                                            wire:click="completePlan"
                                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all"
                                        >
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            إنهاء خطة العمل
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Section --}}
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 ml-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-blue-900 mb-1">نصائح للكتابة:</h3>
                                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                    <li>استخدم الذكاء الاصطناعي لتوليد محتوى احترافي وموثوق</li>
                                    <li>يمكنك تعديل المحتوى المولد بواسطة AI بما يناسب احتياجاتك</li>
                                    <li>احفظ عملك بشكل دوري لتجنب فقدان البيانات</li>
                                    <li>تأكد من مراجعة جميع الفصول قبل إنهاء خطة العمل</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- No Chapter Selected --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">لم يتم اختيار فصل</h3>
                            <p class="mt-2 text-sm text-gray-500">اختر فصلاً من القائمة الجانبية للبدء في الكتابة</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-lg shadow-lg border-2 px-6 py-4 max-w-md"
             :class="{ 'border-green-500': type === 'success', 'border-red-500': type === 'error', 'border-blue-500': type === 'info' }">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="type === 'error'" class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>
