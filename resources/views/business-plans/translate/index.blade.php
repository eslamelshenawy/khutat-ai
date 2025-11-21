<x-layouts.app>
    <x-slot name="title">ترجمة الخطة - {{ $businessPlan->title }}</x-slot>

    <div class="min-h-screen bg-gray-50 py-8" dir="rtl">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center">
                    <a href="{{ route('business-plans.show', $businessPlan) }}" class="text-gray-600 hover:text-gray-900 ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">ترجمة الخطة</h1>
                        <p class="text-gray-600 mt-1">{{ $businessPlan->title }}</p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Translation Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('business-plans.translate.process', $businessPlan) }}" method="POST" id="translationForm">
                    @csrf

                    <!-- Language Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">اختر اللغة المستهدفة</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach([
                                'en' => ['name' => 'الإنجليزية', 'flag' => '🇬🇧'],
                                'fr' => ['name' => 'الفرنسية', 'flag' => '🇫🇷'],
                                'es' => ['name' => 'الإسبانية', 'flag' => '🇪🇸'],
                                'de' => ['name' => 'الألمانية', 'flag' => '🇩🇪'],
                                'it' => ['name' => 'الإيطالية', 'flag' => '🇮🇹'],
                                'pt' => ['name' => 'البرتغالية', 'flag' => '🇵🇹'],
                                'ru' => ['name' => 'الروسية', 'flag' => '🇷🇺'],
                                'zh' => ['name' => 'الصينية', 'flag' => '🇨🇳'],
                                'ja' => ['name' => 'اليابانية', 'flag' => '🇯🇵'],
                                'ko' => ['name' => 'الكورية', 'flag' => '🇰🇷'],
                            ] as $code => $lang)
                                <label class="language-option relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                                    <input type="radio" name="target_language" value="{{ $code }}" class="hidden language-radio" required>
                                    <div class="flex items-center gap-3 w-full">
                                        <span class="text-3xl">{{ $lang['flag'] }}</span>
                                        <span class="text-sm font-medium text-gray-700 language-name">{{ $lang['name'] }}</span>
                                    </div>
                                    <svg class="w-6 h-6 text-indigo-600 absolute left-2 top-1/2 -translate-y-1/2 checkmark hidden" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">خيارات الترجمة</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="include_chapters" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="mr-3">
                                    <div class="text-sm font-medium text-gray-900">ترجمة الفصول</div>
                                    <div class="text-xs text-gray-500">ترجمة محتوى جميع الفصول (قد يستغرق وقتاً أطول)</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-r-4 border-blue-500 p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 ml-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-semibold mb-1">معلومات مهمة:</p>
                                <ul class="list-disc mr-5 space-y-1">
                                    <li>سيتم ترجمة الخطة باستخدام الذكاء الاصطناعي المتقدم</li>
                                    <li>قد تستغرق العملية عدة دقائق حسب حجم الخطة</li>
                                    <li>يمكنك تصدير النسخة المترجمة بعد الانتهاء</li>
                                    <li>الخطة الأصلية لن تتأثر بعملية الترجمة</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">ملخص الخطة</h3>
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">عدد الفصول:</span>
                                <span class="font-semibold text-gray-900 mr-2">{{ $businessPlan->chapters->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">إجمالي الكلمات:</span>
                                <span class="font-semibold text-gray-900 mr-2">{{ $businessPlan->chapters->sum('word_count') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('business-plans.show', $businessPlan) }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                            إلغاء
                        </a>
                        <button type="submit" id="translateBtn" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            <span>بدء الترجمة</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">جاري الترجمة...</h3>
            <p class="text-sm text-gray-600">
                يرجى الانتظار بينما نقوم بترجمة خطتك. قد تستغرق هذه العملية عدة دقائق.
            </p>
        </div>
    </div>

    <style>
        .language-option input:checked ~ .checkmark {
            display: block !important;
        }
        .language-option:has(input:checked) {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
        }
        .language-option:has(input:checked) .language-name {
            color: #4f46e5 !important;
            font-weight: 600;
        }
    </style>

    <script>
        // Handle form submission
        document.getElementById('translationForm').addEventListener('submit', function() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        });

        // Handle language selection visual feedback
        document.querySelectorAll('.language-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selection from all options
                document.querySelectorAll('.language-option').forEach(label => {
                    label.classList.remove('border-indigo-600', 'bg-indigo-50');
                    label.querySelector('.language-name').classList.remove('text-indigo-600', 'font-semibold');
                    label.querySelector('.checkmark').classList.add('hidden');
                });

                // Add selection to chosen option
                if (this.checked) {
                    const label = this.closest('.language-option');
                    label.classList.add('border-indigo-600', 'bg-indigo-50');
                    label.querySelector('.language-name').classList.add('text-indigo-600', 'font-semibold');
                    label.querySelector('.checkmark').classList.remove('hidden');
                }
            });
        });
    </script>

    @livewireScripts
</x-layouts.app>
