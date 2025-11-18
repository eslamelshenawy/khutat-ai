<?php

namespace App\Livewire\Wizard;

use App\Models\BusinessPlan;
use App\Models\BusinessPlanData;
use App\Models\Chapter;
use Livewire\Component;

class WizardSteps extends Component
{
    public $businessPlan;
    public $currentStep = 1;
    public $totalSteps = 8;
    public $wizardData = [];

    // Step fields
    public $vision = '';
    public $mission = '';
    public $targetMarket = '';
    public $competitiveAdvantage = '';
    public $products = [];
    public $marketingStrategy = '';
    public $financialProjections = '';
    public $teamStructure = '';

    public function mount($businessPlan)
    {
        $this->businessPlan = BusinessPlan::findOrFail($businessPlan);

        // Load existing data
        $this->loadWizardData();
    }

    public function loadWizardData()
    {
        $savedData = BusinessPlanData::where('business_plan_id', $this->businessPlan->id)->get();

        foreach ($savedData as $data) {
            $this->wizardData[$data->field_key] = $data->field_value;
        }

        // Populate fields from saved data
        $this->vision = $this->wizardData['vision'] ?? '';
        $this->mission = $this->wizardData['mission'] ?? '';
        $this->targetMarket = $this->wizardData['target_market'] ?? '';
        $this->competitiveAdvantage = $this->wizardData['competitive_advantage'] ?? '';
        $this->marketingStrategy = $this->wizardData['marketing_strategy'] ?? '';
        $this->financialProjections = $this->wizardData['financial_projections'] ?? '';
        $this->teamStructure = $this->wizardData['team_structure'] ?? '';
    }

    public function nextStep()
    {
        $this->saveCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->updateProgress();
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        $this->saveCurrentStep();
        $this->currentStep = $step;
    }

    public function saveCurrentStep()
    {
        $dataToSave = [];

        switch ($this->currentStep) {
            case 1:
                $dataToSave = ['vision' => $this->vision, 'mission' => $this->mission];
                break;
            case 2:
                $dataToSave = ['target_market' => $this->targetMarket];
                break;
            case 3:
                $dataToSave = ['competitive_advantage' => $this->competitiveAdvantage];
                break;
            case 4:
                $dataToSave = ['products' => json_encode($this->products)];
                break;
            case 5:
                $dataToSave = ['marketing_strategy' => $this->marketingStrategy];
                break;
            case 6:
                $dataToSave = ['financial_projections' => $this->financialProjections];
                break;
            case 7:
                $dataToSave = ['team_structure' => $this->teamStructure];
                break;
        }

        foreach ($dataToSave as $key => $value) {
            BusinessPlanData::updateOrCreate(
                [
                    'business_plan_id' => $this->businessPlan->id,
                    'field_key' => $key,
                ],
                [
                    'field_value' => $value,
                    'field_type' => is_array($value) ? 'array' : 'text',
                    'wizard_step' => $this->currentStep,
                ]
            );
        }

        $this->updateProgress();
    }

    public function updateProgress()
    {
        $completionPercentage = ($this->currentStep / $this->totalSteps) * 100;

        $this->businessPlan->update([
            'completion_percentage' => $completionPercentage,
            'status' => $completionPercentage >= 100 ? 'completed' : 'in_progress',
        ]);
    }

    public function finishWizard()
    {
        $this->saveCurrentStep();

        // Generate chapters from wizard data
        $this->generateChapters();

        // Redirect to chapter editor
        return redirect()->route('chapters.edit', ['businessPlan' => $this->businessPlan->id]);
    }

    public function generateChapters()
    {
        $chapterTypes = [
            'executive_summary',
            'company_description',
            'market_analysis',
            'organization_management',
            'products_services',
            'marketing_sales',
            'financial_projections',
            'appendix'
        ];

        foreach ($chapterTypes as $index => $type) {
            Chapter::create([
                'business_plan_id' => $this->businessPlan->id,
                'chapter_number' => $index + 1,
                'chapter_type' => $type,
                'title' => $this->getChapterTitle($type),
                'content' => '',
                'is_ai_generated' => false,
                'order' => $index + 1,
            ]);
        }
    }

    private function getChapterTitle($type)
    {
        $titles = [
            'executive_summary' => 'الملخص التنفيذي',
            'company_description' => 'وصف الشركة',
            'market_analysis' => 'تحليل السوق',
            'organization_management' => 'الهيكل التنظيمي والإدارة',
            'products_services' => 'المنتجات والخدمات',
            'marketing_sales' => 'استراتيجية التسويق والمبيعات',
            'financial_projections' => 'التوقعات المالية',
            'appendix' => 'الملاحق',
        ];

        return $titles[$type] ?? $type;
    }

    public function render()
    {
        return view('livewire.wizard.wizard-steps');
    }
}
