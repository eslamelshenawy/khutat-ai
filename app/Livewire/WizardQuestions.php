<?php

namespace App\Livewire;

use App\Models\BusinessPlan;
use App\Models\WizardStep;
use App\Services\OllamaService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
#[Title('أسئلة خطة العمل')]
class WizardQuestions extends Component
{
    public BusinessPlan $plan;
    public $steps = [];
    public $stepNavigation = []; // Lightweight navigation data
    public $currentStepIndex = 0;
    public $currentStep = null;
    public $answers = [];
    public $progress = 0;
    public $lastSaved = null;

    // AI suggestion properties
    public $aiGenerating = false;
    public $aiGeneratingField = null;

    // Cache duration in seconds (1 hour)
    protected const CACHE_TTL = 3600;

    public function mount($businessPlan)
    {
        $this->plan = BusinessPlan::findOrFail($businessPlan);

        // Check authorization
        Gate::authorize('view', $this->plan);

        // Load steps from cache or database
        $this->steps = $this->getCachedSteps();

        // Build lightweight navigation array
        $this->stepNavigation = collect($this->steps)->map(fn($s) => [
            'id' => $s['id'],
            'title' => $s['title'],
            'icon' => $s['icon'] ?? '📝',
        ])->toArray();

        // Load existing answers if any
        $this->loadAnswers();

        // Set first step as current
        if (count($this->steps) > 0) {
            $this->currentStep = $this->steps[0];
            $this->calculateProgress();
        }
    }

    /**
     * Get wizard steps from cache or load from database
     */
    protected function getCachedSteps(): array
    {
        $cacheKey = 'wizard_steps_v2';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $stepsCollection = WizardStep::with(['activeQuestions', 'boltForm.sections.fields'])
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            return $stepsCollection->map(fn($step) => $this->stepToArray($step))->values()->toArray();
        });
    }

    /**
     * Clear cached steps (call when steps are updated)
     */
    public static function clearStepsCache(): void
    {
        Cache::forget('wizard_steps_v2');
    }

    protected function loadAnswers()
    {
        // Load answers from business plan wizard_data field
        $wizardData = $this->plan->wizard_data ?? [];

        if (is_string($wizardData)) {
            $wizardData = json_decode($wizardData, true) ?? [];
        }

        $this->answers = $wizardData;
    }

    public function goToStep($index)
    {
        if ($index >= 0 && $index < count($this->steps)) {
            // Save current step answers before switching
            $this->saveCurrentStep();

            $this->currentStepIndex = $index;
            $this->currentStep = $this->steps[$index];
            $this->calculateProgress();
        }
    }

    public function nextStep()
    {
        // Validate current step
        if (!$this->validateCurrentStep()) {
            return;
        }

        // Save current answers
        $this->saveCurrentStep();

        if ($this->currentStepIndex < count($this->steps) - 1) {
            $this->currentStepIndex++;
            $this->currentStep = $this->steps[$this->currentStepIndex];
            $this->calculateProgress();
        } else {
            // Last step - finish wizard
            $this->finish();
        }
    }

    public function previousStep()
    {
        // Save current answers
        $this->saveCurrentStep();

        if ($this->currentStepIndex > 0) {
            $this->currentStepIndex--;
            $this->currentStep = $this->steps[$this->currentStepIndex];
            $this->calculateProgress();
        }
    }

    protected function validateCurrentStep()
    {
        $rules = [];
        $messages = [];

        // Validate Bolt Form fields
        if ($this->currentStep && isset($this->currentStep['bolt_form_sections'])) {
            foreach ($this->currentStep['bolt_form_sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    $fieldKey = 'bolt_' . $field['id'];
                    $fieldName = $field['name'];
                    $validationRules = [];

                    if (!empty($field['is_required'])) {
                        $validationRules[] = 'required';
                    } else {
                        $validationRules[] = 'nullable';
                    }

                    // Add type-specific validation for Bolt fields (types are lowercase)
                    switch ($field['type']) {
                        case 'text':
                        case 'textinput':
                            $validationRules[] = 'string';
                            $validationRules[] = 'max:500';
                            break;
                        case 'textarea':
                        case 'richeditor':
                        case 'paragraph':
                            $validationRules[] = 'string';
                            $validationRules[] = 'max:10000';
                            break;
                        case 'number':
                        case 'numberinput':
                            $validationRules[] = 'numeric';
                            $validationRules[] = 'min:0';
                            break;
                        case 'date':
                        case 'datepicker':
                            $validationRules[] = 'date';
                            break;
                        case 'email':
                            $validationRules[] = 'email';
                            break;
                        case 'select':
                        case 'radio':
                            $validationRules[] = 'string';
                            break;
                        case 'checkbox':
                        case 'toggle':
                            // These can be string or array
                            break;
                    }

                    $rules["answers.$fieldKey"] = $validationRules;

                    // Add comprehensive Arabic messages for this field
                    $this->addArabicMessages($messages, "answers.$fieldKey", $fieldName);
                }
            }
        }

        // Validate legacy questions
        if ($this->currentStep && isset($this->currentStep['active_questions'])) {
            foreach ($this->currentStep['active_questions'] as $question) {
                $fieldName = $question['field_name'];
                $fieldLabel = $question['label'];
                $validationRules = [];

                if ($question['is_required']) {
                    $validationRules[] = 'required';
                } else {
                    $validationRules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($question['type']) {
                    case 'text':
                        $validationRules[] = 'string';
                        $validationRules[] = 'max:500';
                        break;
                    case 'textarea':
                        $validationRules[] = 'string';
                        $validationRules[] = 'max:10000';
                        break;
                    case 'number':
                        $validationRules[] = 'numeric';
                        $validationRules[] = 'min:0';
                        break;
                    case 'date':
                        $validationRules[] = 'date';
                        break;
                    case 'email':
                        $validationRules[] = 'email';
                        break;
                    case 'checkbox':
                        // Checkbox can be array (multiple options) or single value
                        break;
                }

                // Merge custom validation rules if any
                if (!empty($question['validation_rules'])) {
                    $validationRules = array_merge($validationRules, $question['validation_rules']);
                }

                $rules["answers.$fieldName"] = $validationRules;

                // Add comprehensive Arabic messages for this field
                $this->addArabicMessages($messages, "answers.$fieldName", $fieldLabel);
            }
        }

        $this->validate($rules, $messages);
        return true;
    }

    /**
     * Add comprehensive Arabic validation messages for a field
     */
    protected function addArabicMessages(array &$messages, string $fieldKey, string $fieldLabel): void
    {
        $messages["$fieldKey.required"] = "حقل {$fieldLabel} مطلوب";
        $messages["$fieldKey.string"] = "حقل {$fieldLabel} يجب أن يكون نصاً";
        $messages["$fieldKey.numeric"] = "حقل {$fieldLabel} يجب أن يكون رقماً";
        $messages["$fieldKey.integer"] = "حقل {$fieldLabel} يجب أن يكون رقماً صحيحاً";
        $messages["$fieldKey.email"] = "حقل {$fieldLabel} يجب أن يكون بريداً إلكترونياً صحيحاً";
        $messages["$fieldKey.date"] = "حقل {$fieldLabel} يجب أن يكون تاريخاً صحيحاً";
        $messages["$fieldKey.url"] = "حقل {$fieldLabel} يجب أن يكون رابطاً صحيحاً";
        $messages["$fieldKey.min"] = "حقل {$fieldLabel} يجب أن يكون على الأقل :min";
        $messages["$fieldKey.max"] = "حقل {$fieldLabel} يجب ألا يتجاوز :max حرف";
        $messages["$fieldKey.min.numeric"] = "حقل {$fieldLabel} يجب أن يكون على الأقل :min";
        $messages["$fieldKey.max.numeric"] = "حقل {$fieldLabel} يجب ألا يتجاوز :max";
        $messages["$fieldKey.between"] = "حقل {$fieldLabel} يجب أن يكون بين :min و :max";
        $messages["$fieldKey.in"] = "القيمة المختارة في حقل {$fieldLabel} غير صحيحة";
        $messages["$fieldKey.array"] = "حقل {$fieldLabel} يجب أن يكون قائمة";
        $messages["$fieldKey.boolean"] = "حقل {$fieldLabel} يجب أن يكون نعم أو لا";
        $messages["$fieldKey.confirmed"] = "تأكيد حقل {$fieldLabel} غير متطابق";
        $messages["$fieldKey.unique"] = "قيمة حقل {$fieldLabel} مستخدمة مسبقاً";
        $messages["$fieldKey.exists"] = "قيمة حقل {$fieldLabel} غير موجودة";
        $messages["$fieldKey.regex"] = "صيغة حقل {$fieldLabel} غير صحيحة";
        $messages["$fieldKey.after"] = "حقل {$fieldLabel} يجب أن يكون تاريخاً بعد :date";
        $messages["$fieldKey.before"] = "حقل {$fieldLabel} يجب أن يكون تاريخاً قبل :date";
        $messages["$fieldKey.digits"] = "حقل {$fieldLabel} يجب أن يتكون من :digits أرقام";
        $messages["$fieldKey.size"] = "حقل {$fieldLabel} يجب أن يكون بحجم :size";
    }

    protected function saveCurrentStep()
    {
        // Update business plan with current answers
        $this->plan->update([
            'wizard_data' => $this->answers,
        ]);
    }

    public function autoSave()
    {
        $this->plan->update([
            'wizard_data' => $this->answers,
        ]);
        $this->lastSaved = now()->format('H:i:s');
        $this->dispatch('auto-saved', time: $this->lastSaved);
    }

    protected function calculateProgress()
    {
        $totalSteps = count($this->steps);
        if ($totalSteps > 0) {
            $this->progress = (($this->currentStepIndex + 1) / $totalSteps) * 100;
        }
    }

    /**
     * Convert a WizardStep model to array with all needed data
     */
    protected function stepToArray($step): array
    {
        $data = [
            'id' => $step->id,
            'title' => $step->title,
            'description' => $step->description,
            'icon' => $step->icon,
            'order' => $step->order,
            'is_active' => $step->is_active,
            'bolt_form_id' => $step->bolt_form_id,
            'active_questions' => $step->activeQuestions->toArray(),
            'bolt_form' => null,
            'bolt_form_sections' => [],
        ];

        // Add Bolt Form data if exists
        if ($step->boltForm) {
            $data['bolt_form'] = [
                'id' => $step->boltForm->id,
                'name' => $step->boltForm->name,
                'slug' => $step->boltForm->slug,
            ];
            $data['bolt_form_sections'] = $step->boltForm->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'fields' => $section->fields->map(function ($field) {
                        // Convert Bolt field type class to simple type name
                        $type = $field->type;
                        if (str_contains($type, '\\')) {
                            $type = class_basename($type);
                        }
                        $type = strtolower($type);

                        // Normalize options to consistent format
                        $options = [];
                        if (is_array($field->options)) {
                            foreach ($field->options as $key => $value) {
                                if (is_array($value)) {
                                    $options[] = [
                                        'value' => $value['value'] ?? $value[0] ?? $key,
                                        'label' => $value['label'] ?? $value[1] ?? $value['value'] ?? $value[0] ?? $key,
                                    ];
                                } else {
                                    $options[] = [
                                        'value' => $key,
                                        'label' => $value,
                                    ];
                                }
                            }
                        }

                        return [
                            'id' => $field->id,
                            'name' => $field->name,
                            'type' => $type,
                            'options' => $options,
                            'is_required' => $field->is_required ?? false,
                            'html_id' => $field->html_id ?? 'bolt_field_' . $field->id,
                        ];
                    })->toArray(),
                ];
            })->toArray();
        }

        return $data;
    }

    public function finish()
    {
        // Validate all steps
        if (!$this->validateCurrentStep()) {
            return;
        }

        // Save final answers
        $this->saveCurrentStep();

        // Update business plan completion
        $this->plan->update([
            'wizard_data' => $this->answers,
            'wizard_completed' => true,
            'completion_percentage' => 10, // Initial percentage after questions
        ]);

        // Dispatch success notification
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'تم حفظ إجاباتك بنجاح! سننتقل الآن لكتابة محتوى خطة العمل.',
        ]);

        // Redirect to chapters editor
        return redirect()->route('chapters.edit', ['businessPlan' => $this->plan->id]);
    }


    /**
     * Generate AI suggestion for a field
     */
    public function generateAISuggestion(string $fieldKey, string $fieldName)
    {
        $this->aiGenerating = true;
        $this->aiGeneratingField = $fieldKey;

        try {
            $ollama = new OllamaService();
            $prompt = $this->buildFieldSuggestionPrompt($fieldName);
            $suggestion = $ollama->chatWithAI($prompt);

            // Set the suggestion as the field value
            $this->answers[$fieldKey] = $suggestion;

            $this->dispatch("notify", [
                "type" => "success",
                "message" => "تم توليد الاقتراح بنجاح!",
            ]);
        } catch (\Exception $e) {
            Log::error("AI suggestion failed", [
                "field" => $fieldKey,
                "error" => $e->getMessage(),
            ]);

            $this->dispatch("notify", [
                "type" => "error",
                "message" => "حدث خطأ في توليد الاقتراح: " . $e->getMessage(),
            ]);
        } finally {
            $this->aiGenerating = false;
            $this->aiGeneratingField = null;
        }
    }

    /**
     * Build prompt for AI suggestion based on field name and context
     */
    protected function buildFieldSuggestionPrompt(string $fieldName): string
    {
        $planName = $this->plan->name ?? "خطة عمل";
        $currentStepTitle = $this->currentStep["title"] ?? "";

        // Gather context from existing answers
        $context = "";
        foreach ($this->answers as $key => $value) {
            if (!empty($value) && is_string($value)) {
                $context .= $value . "\n";
            }
        }

        return "أنت مساعد ذكي لكتابة خطط العمل باللغة العربية.

اسم خطة العمل: {$planName}
القسم الحالي: {$currentStepTitle}
السؤال المطلوب الإجابة عليه: {$fieldName}

المعلومات المتوفرة حتى الآن:
{$context}

اكتب إجابة مناسبة ومهنية للسؤال أعلاه. يجب أن تكون الإجابة:
- واضحة ومختصرة
- مناسبة لسياق خطة العمل
- باللغة العربية الفصحى
- عملية وقابلة للتطبيق

الإجابة:";
    }

    public function render()
    {
        return view('livewire.wizard-questions');
    }
}
