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

                            @if($field['type'] === 'text' || $field['type'] === 'textInput')
                                <input
                                    type="text"
                                    wire:model="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                >

                            @elseif($field['type'] === 'textarea' || $field['type'] === 'richEditor')
                                <textarea
                                    wire:model="answers.{{ $fieldKey }}"
                                    rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل إجابتك هنا..."
                                ></textarea>

                            @elseif($field['type'] === 'number' || $field['type'] === 'numberInput')
                                <input
                                    type="number"
                                    wire:model="answers.{{ $fieldKey }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="أدخل الرقم..."
                                >

                            @elseif($field['type'] === 'date' || $field['type'] === 'datePicker')
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
                                        <option value="{{ is_array($option) ? ($option['value'] ?? $option[0] ?? '') : $option }}">
                                            {{ is_array($option) ? ($option['label'] ?? $option[1] ?? $option['value'] ?? $option[0] ?? '') : $option }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>

                            @elseif($field['type'] === 'radio')
                                <div class="space-y-3">
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $optionKey => $option)
                                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="radio"
                                                wire:model="answers.{{ $fieldKey }}"
                                                value="{{ is_array($option) ? ($option['value'] ?? $option[0] ?? $optionKey) : $optionKey }}"
                                                class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                            >
                                            <span class="mr-3 text-gray-900">{{ is_array($option) ? ($option['label'] ?? $option[1] ?? $option['value'] ?? $option[0] ?? '') : $option }}</span>
                                        </label>
                                        @endforeach
                                    @endif
                                </div>

                            @elseif($field['type'] === 'checkbox' || $field['type'] === 'toggle')
                                <div class="space-y-3">
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $optionKey => $option)
                                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="answers.{{ $fieldKey }}"
                                                value="{{ is_array($option) ? ($option['value'] ?? $option[0] ?? $optionKey) : $optionKey }}"
                                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                            >
                                            <span class="mr-3 text-gray-900">{{ is_array($option) ? ($option['label'] ?? $option[1] ?? $option['value'] ?? $option[0] ?? '') : $option }}</span>
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
        <div class="flex justify-between items-center gap-4">
            @if($currentStepIndex > 0)
            <button
                wire:click="previousStep"
                class="px-6 py-3 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                السابق
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
                class="px-6 py-3 bg-gradient-to-l from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition flex items-center gap-2 font-bold"
            >
                @if($currentStepIndex < count($steps) - 1)
                    التالي
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @else
                    إنهاء وحفظ
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
            </button>
        </div>
        @endif
    </div>
</div>
