<x-layouts.app>
    <x-slot name="title">الترجمة - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('business-plans.translate', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">النسخة المترجمة</h1>
                            <p class="text-gray-600 mt-1">{{ $languageName }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('business-plans.translate.export', $businessPlan) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="target_language" value="{{ $targetLanguage }}">
                            <input type="hidden" name="translated_data" value="{{ json_encode($translatedData) }}">
                            <input type="hidden" name="translated_chapters" value="{{ json_encode($translatedChapters) }}">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                تصدير PDF
                            </button>
                        </form>
                        <form action="{{ route('business-plans.translate.export', $businessPlan) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="target_language" value="{{ $targetLanguage }}">
                            <input type="hidden" name="translated_data" value="{{ json_encode($translatedData) }}">
                            <input type="hidden" name="translated_chapters" value="{{ json_encode($translatedChapters) }}">
                            <input type="hidden" name="format" value="word">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                تصدير Word
                            </button>
                        </form>
                        <form action="{{ route('business-plans.translate.export', $businessPlan) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="target_language" value="{{ $targetLanguage }}">
                            <input type="hidden" name="translated_data" value="{{ json_encode($translatedData) }}">
                            <input type="hidden" name="translated_chapters" value="{{ json_encode($translatedChapters) }}">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                تصدير Excel
                            </button>
                        </form>
                        <form action="{{ route('business-plans.translate.export', $businessPlan) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="target_language" value="{{ $targetLanguage }}">
                            <input type="hidden" name="translated_data" value="{{ json_encode($translatedData) }}">
                            <input type="hidden" name="translated_chapters" value="{{ json_encode($translatedChapters) }}">
                            <input type="hidden" name="format" value="powerpoint">
                            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                                تصدير PowerPoint
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>تمت الترجمة بنجاح إلى {{ $languageName }}</span>
                </div>
            </div>

            <!-- Translated Content -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <!-- Title -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $translatedData['title'] }}</h2>
                    <div class="flex items-center text-sm text-gray-500 gap-4">
                        <span>{{ $translatedData['company_name'] }}</span>
                    </div>
                </div>

                <!-- Description -->
                @if(!empty($translatedData['description']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">الوصف</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $translatedData['description'] }}</p>
                    </div>
                @endif

                <!-- Vision -->
                @if(!empty($translatedData['vision']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">الرؤية</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $translatedData['vision'] }}</p>
                    </div>
                @endif

                <!-- Mission -->
                @if(!empty($translatedData['mission']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">الرسالة</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $translatedData['mission'] }}</p>
                    </div>
                @endif

                <!-- Target Market -->
                @if(!empty($translatedData['target_market']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">السوق المستهدف</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $translatedData['target_market'] }}</p>
                    </div>
                @endif
            </div>

            <!-- Translated Chapters -->
            @if(count($translatedChapters) > 0)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900">الفصول</h2>
                    @foreach($translatedChapters as $index => $chapter)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900">{{ $chapter['title'] }}</h3>
                            </div>
                            <div class="text-gray-700 leading-relaxed prose max-w-none">
                                {{ $chapter['content'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('business-plans.show', $businessPlan) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة إلى الخطة الأصلية
                </a>
            </div>
        </div>
    </div>

    @livewireScripts
</x-layouts.app>
