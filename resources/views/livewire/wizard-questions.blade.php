<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8"
     x-data="{
         autoSaveTime: null,
         init() {
             setInterval(() => { @this.call('autoSave') }, 15000);
         }
     }"
     @auto-saved.window="autoSaveTime = $event.detail.time">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <h2 class="text-sm font-medium text-gray-700">خطوة {{ $currentStepIndex + 1 }} من {{ count($steps) }}</h2>
                    <span x-show="autoSaveTime" x-cloak class="text-xs text-green-600">
                        ✓ حفظ تلقائي <span x-text="autoSaveTime"></span>
                    </span>
                </div>
                <span class="text-sm text-gray-600">{{ number_format($progress, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-l from-blue-600 to-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Steps Navigation (Icons) -->
        <div class="flex justify-between mb-8 overflow-x-auto">
            @foreach($steps as $index => $step)
            <button
                wire:click="goToStep({{ $index }})"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50"
                class="flex flex-col items-center min-w-0 px-2 group {{ $index === $currentStepIndex ? 'opacity-100' : 'opacity-50 hover:opacity-75' }}"
                title="{{ $step['title'] }}"
            >
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-2xl sm:text-3xl mb-2 transition-all
                    {{ $index === $currentStepIndex ? 'bg-gradient-to-l from-blue-600 to-indigo-600 scale-110' : 'bg-white border-2 border-gray-300' }}">
                    <span>{{ $step['icon'] ?? '📝' }}</span>
                </div>
                <span class="text-[10px] sm:text-xs text-center font-medium text-gray-700 group-hover:text-blue-600 hidden sm:block">
                    {{ \Illuminate\Support\Str::limit($step['title'], 15) }}
                </span>
            </button>
            @endforeach
        </div>

        @if($currentStep)
        <!-- Current Step Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 mb-6">
            <!-- Step Header -->
            <div class="mb-6">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-l from-blue-600 to-indigo-600 flex items-center justify-center text-4xl">
                        {{ $currentStep['icon'] ?? '📝' }}
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $currentStep['title'] }}</h1>
                        @if($currentStep['description'])
                        <p class="text-gray-600 mt-1">{{ $currentStep['description'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bolt Form Fields (if exists) -->
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
                                <textarea
                                    wire:model.live.debounce.500ms="answers.{{ $fieldKey }}"
                                    rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                ></textarea>

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

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center gap-4" wire:loading.class="pointer-events-none">
            @if($currentStepIndex > 0)
            <button
                wire:click="previousStep"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                class="px-6 py-3 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 disabled:opacity-50"
            >
                <span wire:loading wire:target="previousStep,nextStep,goToStep">
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <svg wire:loading.remove wire:target="previousStep,nextStep,goToStep" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span wire:loading.remove wire:target="previousStep">السابق</span>
                <span wire:loading wire:target="previousStep">جاري التحميل...</span>
            </button>
            @else
            <a
                href="{{ route('wizard.start') }}"
                class="px-6 py-3 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                رجوع
            </a>
            @endif

            <button
                wire:click="nextStep"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                class="px-6 py-3 bg-gradient-to-l from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition flex items-center gap-2 font-bold disabled:opacity-50"
            >
                <span wire:loading wire:target="nextStep,previousStep,goToStep">
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                @if($currentStepIndex < count($steps) - 1)
                    <span wire:loading.remove wire:target="nextStep">التالي</span>
                    <span wire:loading wire:target="nextStep">جاري التحميل...</span>
                    <svg wire:loading.remove wire:target="nextStep,previousStep,goToStep" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @else
                    <span wire:loading.remove wire:target="nextStep">إنهاء وحفظ</span>
                    <span wire:loading wire:target="nextStep">جاري الحفظ...</span>
                    <svg wire:loading.remove wire:target="nextStep,previousStep,goToStep" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
            </button>
        </div>
        @endif
    </div>
</div>
