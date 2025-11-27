<?php

namespace App\Livewire;

use App\Models\BusinessPlan;
use App\Models\WizardStep;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
#[Title('أسئلة خطة العمل')]
class WizardQuestions extends Component
{
    public BusinessPlan $plan;
    public $steps = [];
    public $currentStepIndex = 0;
    public $currentStep = null;
    public $answers = [];
    public $progress = 0;
    public $lastSaved = null;

    public function mount($businessPlan)
    {
        $this->plan = BusinessPlan::findOrFail($businessPlan);

        // Check authorization
        Gate::authorize('view', $this->plan);

        // Load active wizard steps with their questions and bolt forms
        $this->steps = WizardStep::with(['activeQuestions', 'boltForm.sections.fields'])
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Load existing answers if any
        $this->loadAnswers();

        // Set first step as current
        if ($this->steps->count() > 0) {
            $this->currentStep = $this->stepToArray($this->steps->first());
            $this->calculateProgress();
        }
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
        if ($index >= 0 && $index < $this->steps->count()) {
            // Save current step answers before switching
            $this->saveCurrentStep();

            $this->currentStepIndex = $index;
            $this->currentStep = $this->stepToArray($this->steps->get($index));
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

        if ($this->currentStepIndex < $this->steps->count() - 1) {
            $this->currentStepIndex++;
            $this->currentStep = $this->stepToArray($this->steps->get($this->currentStepIndex));
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
            $this->currentStep = $this->stepToArray($this->steps->get($this->currentStepIndex));
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
                    $validationRules = [];

                    if (!empty($field['is_required'])) {
                        $validationRules[] = 'required';
                    } else {
                        $validationRules[] = 'nullable';
                    }

                    // Add type-specific validation for Bolt fields
                    switch ($field['type']) {
                        case 'text':
                        case 'textInput':
                            $validationRules[] = 'string';
                            $validationRules[] = 'max:500';
                            break;
                        case 'textarea':
                        case 'richEditor':
                            $validationRules[] = 'string';
                            break;
                        case 'number':
                        case 'numberInput':
                            $validationRules[] = 'numeric';
                            break;
                        case 'date':
                        case 'datePicker':
                            $validationRules[] = 'date';
                            break;
                    }

                    $rules["answers.$fieldKey"] = $validationRules;
                    $messages["answers.$fieldKey.required"] = "حقل {$field['name']} مطلوب";
                }
            }
        }

        // Validate legacy questions
        if ($this->currentStep && isset($this->currentStep['active_questions'])) {
            foreach ($this->currentStep['active_questions'] as $question) {
                $fieldName = $question['field_name'];
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
                        break;
                    case 'number':
                        $validationRules[] = 'numeric';
                        break;
                    case 'date':
                        $validationRules[] = 'date';
                        break;
                    case 'checkbox':
                        // Checkbox can be array (multiple options) or single value
                        // Don't add strict array validation - let it be flexible
                        break;
                }

                // Merge custom validation rules if any
                if (!empty($question['validation_rules'])) {
                    $validationRules = array_merge($validationRules, $question['validation_rules']);
                }

                $rules["answers.$fieldName"] = $validationRules;

                $messages["answers.$fieldName.required"] = "حقل {$question['label']} مطلوب";
            }
        }

        $this->validate($rules, $messages);
        return true;
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
        $totalSteps = $this->steps->count();
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
                        return [
                            'id' => $field->id,
                            'name' => $field->name,
                            'type' => $field->type,
                            'options' => $field->options,
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

    public function render()
    {
        return view('livewire.wizard-questions');
    }
}
