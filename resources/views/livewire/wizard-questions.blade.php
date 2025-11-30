<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Chapters List -->
        <div class="w-80 bg-white shadow-xl border-l border-gray-200 overflow-y-auto flex-shrink-0">
            <div class="sticky top-0 z-10 bg-gradient-to-l from-indigo-600 to-indigo-700 px-6 py-5 shadow-md">
                <h2 class="text-xl font-bold text-white">{{ $plan->name ?? "خطة عمل" }}</h2>
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
                        <span class="text-indigo-600 font-bold">{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-l from-indigo-500 to-indigo-600 h-2 rounded-full transition-all duration-500"
                             style="width: {{ number_format($progress, 0) }}%"></div>
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
                    @foreach($steps as $index => $step)
                        <button
                            data-chapter-id="{{ $index }}"
                            wire:click="goToStep({{ $index }})"
                            class="chapter-item w-full text-right px-4 py-3.5 rounded-lg transition-all duration-200 group
                                   {{ $index === $currentStepIndex
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
                                            {{ $index === $currentStepIndex
                                               ? 'bg-indigo-600 text-white'
                                               : 'bg-gray-200 text-gray-600 group-hover:bg-gray-300' }}">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $step["title"] }}</span>
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

                                @if($index === $currentStepIndex)
                                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </button>
                    
                        
                    @endforeach
                </div>

                <a href="{{ route('wizard.start', $plan) }}"
                   class="mt-6 w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    رجوع للخطط
                </a>
            </div>
        </div>

        <!-- Main Editor Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white">
            <!-- Top Action Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        @if($currentStep)
                            <h3 class="text-2xl font-bold text-gray-900">{{ $currentStep["title"] }}</h3>
                            <div class="flex items-center gap-4 mt-1.5">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    عدد الكلمات: <span class="font-semibold mr-1">{{ 0 ?? 0 }}</span>
                                </p>
                                @if(false)
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

                    @if($currentStep)
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="autoSave"
                                wire:loading.attr="disabled"
                                wire:target="autoSave"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="autoSave" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                <svg wire:loading wire:target="autoSave" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="autoSave">حفظ</span>
                                <span wire:loading wire:target="autoSave">جاري الحفظ...</span>
                            </button>

                            <button
                                wire:click="generateAllAI"
                                wire:loading.attr="disabled"
                                wire:target="generateAllAI"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="generateAllAI" class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 7H7v6h6V7z"/>
                                    <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd"/>
                                </svg>
                                <svg wire:loading wire:target="generateAllAI" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="generateAllAI">توليد بالذكاء الاصطناعي</span>
                                <span wire:loading wire:target="generateAllAI">جاري التوليد...</span>
                            </button>

                            <button
                                wire:click="improveAllContent"
                                wire:loading.attr="disabled"
                                wire:target="improveAllContent"
                                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-l from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-sm hover:shadow transition-all disabled:opacity-50"
                            >
                                <svg wire:loading.remove wire:target="improveAllContent" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <svg wire:loading wire:target="improveAllContent" class="animate-spin h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="improveAllContent">تحسين المحتوى</span>
                                <span wire:loading wire:target="improveAllContent">جاري التحسين...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Area - Questions -->
            <div class="flex-1 overflow-y-auto p-8 bg-gray-50">
                @if($currentStep)
                    <div class="max-w-5xl mx-auto">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-6">
            @if(isset($currentStep['bolt_form_sections']) && count($currentStep['bolt_form_sections']) > 0)
            <div class="space-y-6 mb-8">
                @foreach($currentStep['bolt_form_sections'] as $section)
                <div class="border-b border-gray-200 pb-6 last:border-b-0">
                    @if($section['name'])
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $section['name'] }}</h3>
                    @endif

                    <div class="space-y-4">
                        @foreach($section['fields'] as $field)
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                {{ $field['name'] }}
                                @if($field['is_required'])
                                <span class="text-red-600">*</span>
                                @endif
                            </label>

                            @php
                                $fieldKey = 'bolt_' . $field['id'];
                            @endphp

                            @if(in_array($field['type'], ['text', 'textinput']))
                                <input
                                    type="text"
                                    wire:model.live.debounce.500ms="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                >

                            @elseif(in_array($field['type'], ['textarea', 'richeditor', 'paragraph']))
                                <div class="relative">
                                    <textarea
                                    wire:model.live.debounce.500ms="answers.{{ $fieldKey }}"
                                    rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                ></textarea>
                                    <button type="button" wire:click="generateAISuggestion('{{ $fieldKey }}', '{{ $field["name"] }}')" wire:loading.attr="disabled" wire:target="generateAISuggestion" class="absolute left-2 bottom-2 px-3 py-1.5 bg-gradient-to-l from-purple-600 to-indigo-600 text-white text-xs rounded-lg hover:from-purple-700 hover:to-indigo-700 transition flex items-center gap-1.5 disabled:opacity-50" title="اقتراح بالذكاء الاصطناعي"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg><span>AI</span></button>
                                </div>

                            @elseif(in_array($field['type'], ['number', 'numberinput']))
                                <input
                                    type="number"
                                    wire:model.live.debounce.500ms="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل الرقم..."
                                >

                            @elseif(in_array($field['type'], ['date', 'datepicker']))
                                <input
                                    type="date"
                                    wire:model="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >

                            @elseif($field['type'] === 'select')
                                <select
                                    wire:model="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">اختر...</option>
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $option)
                                        <option value="{{ $option['value'] ?? '' }}">
                                            {{ $option['label'] ?? '' }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>

                            @elseif($field['type'] === 'radio')
                                <div class="space-y-3">
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $option)
                                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="radio"
                                                wire:model="answers.{{ $fieldKey }}"
                                                value="{{ $option['value'] ?? '' }}"
                                                class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                            >
                                            <span class="mr-3 text-gray-900">{{ $option['label'] ?? '' }}</span>
                                        </label>
                                        @endforeach
                                    @endif
                                </div>

                            @elseif($field['type'] === 'checkbox' || $field['type'] === 'toggle')
                                <div class="space-y-3">
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $option)
                                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="answers.{{ $fieldKey }}"
                                                value="{{ $option['value'] ?? '' }}"
                                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                            >
                                            <span class="mr-3 text-gray-900">{{ $option['label'] ?? '' }}</span>
                                        </label>
                                        @endforeach
                                    @else
                                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="answers.{{ $fieldKey }}"
                                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                            >
                                            <span class="mr-3 text-gray-900">{{ $field['name'] }}</span>
                                        </label>
                                    @endif
                                </div>
                            @else
                                <!-- Default to text input for unknown types -->
                                <input
                                    type="text"
                                    wire:model="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                >
                            @endif

                            @error('answers.' . $fieldKey)
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Questions -->
            <div class="space-y-6">
                @if(isset($currentStep['active_questions']) && count($currentStep['active_questions']) > 0)
                    @foreach($currentStep['active_questions'] as $question)
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            {{ $question['label'] }}
                            @if($question['is_required'])
                            <span class="text-red-600">*</span>
                            @endif
                        </label>

                        @if($question['help_text'])
                        <p class="text-sm text-gray-600 mb-3">{{ $question['help_text'] }}</p>
                        @endif

                        @if($question['type'] === 'text')
                            <input
                                type="text"
                                wire:model="answers.{{ $question['field_name'] }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="أدخل إجابتك هنا..."
                            >

                        @elseif($question['type'] === 'textarea')
                            <textarea
                                wire:model="answers.{{ $question['field_name'] }}"
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="أدخل إجابتك هنا..."
                            ></textarea>

                        @elseif($question['type'] === 'number')
                            <input
                                type="number"
                                wire:model="answers.{{ $question['field_name'] }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="أدخل الرقم..."
                            >

                        @elseif($question['type'] === 'date')
                            <input
                                type="date"
                                wire:model="answers.{{ $question['field_name'] }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >

                        @elseif($question['type'] === 'select')
                            <select
                                wire:model="answers.{{ $question['field_name'] }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">اختر...</option>
                                @if($question['options'])
                                    @foreach($question['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>

                        @elseif($question['type'] === 'radio')
                            <div class="space-y-3">
                                @if($question['options'])
                                    @foreach($question['options'] as $value => $label)
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input
                                            type="radio"
                                            wire:model="answers.{{ $question['field_name'] }}"
                                            value="{{ $value }}"
                                            class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="mr-3 text-gray-900">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                @endif
                            </div>

                        @elseif($question['type'] === 'checkbox')
                            <div class="space-y-3">
                                @if($question['options'])
                                    @foreach($question['options'] as $value => $label)
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="answers.{{ $question['field_name'] }}"
                                            value="{{ $value }}"
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                        >
                                        <span class="mr-3 text-gray-900">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                @endif
                            </div>
                        @endif

                        @error('answers.' . $question['field_name'])
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                @else
                    @if(!isset($currentStep['bolt_form_sections']) || count($currentStep['bolt_form_sections']) == 0)
                    <div class="text-center py-8 text-gray-500">
                        لا توجد أسئلة في هذه الخطوة
                    </div>
                    @endif
                @endif
            </div>

                        </div>
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
            @if($currentStep)
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
            @if($currentStep)
                <div class="bg-gradient-to-l from-gray-50 to-white border-t border-gray-200 px-6 py-4 flex justify-between items-center shadow-inner flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">آخر حفظ:</span>
                        <span class="text-sm font-medium text-gray-900">{{ "منذ لحظات" }}</span>
                    </div>
                    <button
                        wire:click="nextStep"
                        class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-sm font-bold text-white bg-gradient-to-l from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transition-all transform hover:scale-105"
                    >
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @if($currentStepIndex < count($steps) - 1) حفظ والتالي @else حفظ وإنهاء التحرير @endif
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

    </div>
