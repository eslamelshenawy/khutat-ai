<?php

namespace App\Livewire\Wizard;

use App\Models\Template;
use App\Models\BusinessPlan;
use Livewire\Component;
use Illuminate\Support\Str;

class WizardStart extends Component
{
    public $templates;
    public $selectedTemplateId;
    public $title = '';
    public $companyName = '';
    public $industryType = '';
    public $projectType = 'new_business';
    public $description = '';

    public function mount()
    {
        $this->templates = Template::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $template = Template::find($templateId);

        if ($template) {
            $this->industryType = $template->industry_type;
        }
    }

    public function startWizard()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'companyName' => 'required|min:2|max:255',
            'industryType' => 'required|max:100',
            'projectType' => 'required|in:new_business,existing_expansion,franchise,startup',
            'description' => 'nullable|max:1000',
        ]);

        // Create new business plan
        $businessPlan = BusinessPlan::create([
            'user_id' => auth()->id(),
            'template_id' => $this->selectedTemplateId,
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . time(),
            'description' => $this->description,
            'project_type' => $this->projectType,
            'industry_type' => $this->industryType,
            'status' => 'draft',
            'completion_percentage' => 0,
            'company_name' => $this->companyName,
            'language' => 'ar',
            'is_public' => false,
            'allow_comments' => false,
            'version' => 1,
        ]);

        // Redirect to wizard steps
        return redirect()->route('wizard.steps', ['businessPlan' => $businessPlan->id]);
    }

    public function render()
    {
        return view('livewire.wizard.wizard-start');
    }
}
