<?php

namespace App\Livewire;

use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('إنشاء خطة عمل جديدة')]
class WizardStart extends Component
{
    public $templates;
    public $selectedTemplate = null;
    public $projectType = 'new_business';
    public $industryType = '';
    public $companyName = '';
    public $showCustomForm = false;

    public function mount()
    {
        // Load active and featured templates
        $this->templates = Template::where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order')
            ->get();
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplate = $templateId;
        $this->showCustomForm = false;
    }

    public function selectCustom()
    {
        $this->selectedTemplate = null;
        $this->showCustomForm = true;
    }

    public function goBack()
    {
        $this->selectedTemplate = null;
        $this->showCustomForm = false;
    }

    public function startWizard()
    {
        // Validate input
        $this->validate([
            'companyName' => 'required|string|min:3|max:255',
            'projectType' => 'required|in:new_business,existing_expansion,franchise,startup',
            'industryType' => 'required|string|min:2|max:100',
        ], [
            'companyName.required' => 'يرجى إدخال اسم الشركة',
            'companyName.min' => 'اسم الشركة يجب أن يكون 3 أحرف على الأقل',
            'projectType.required' => 'يرجى اختيار نوع المشروع',
            'industryType.required' => 'يرجى إدخال نوع الصناعة',
            'industryType.min' => 'نوع الصناعة يجب أن يكون حرفين على الأقل',
        ]);

        // Create business plan
        $plan = auth()->user()->businessPlans()->create([
            'template_id' => $this->selectedTemplate,
            'title' => 'خطة عمل ' . $this->companyName,
            'slug' => \Illuminate\Support\Str::slug($this->companyName . '-' . time()),
            'company_name' => $this->companyName,
            'project_type' => $this->projectType,
            'industry_type' => $this->industryType,
            'status' => 'draft',
            'completion_percentage' => 0,
            'language' => 'ar',
            'version' => 1,
        ]);

        // Create chapters from template if selected
        if ($this->selectedTemplate) {
            $template = Template::find($this->selectedTemplate);
            if ($template && !empty($template->structure)) {
                // Decode structure if it's a JSON string
                $structure = is_string($template->structure)
                    ? json_decode($template->structure, true)
                    : $template->structure;

                // Get sections array from structure
                $sections = $structure['sections'] ?? $structure ?? [];

                $order = 1;
                foreach ($sections as $chapterType) {
                    $plan->chapters()->create([
                        'title' => $this->getChapterTitle($chapterType),
                        'slug' => \Illuminate\Support\Str::slug($chapterType . '-' . $plan->id),
                        'chapter_type' => \Illuminate\Support\Str::slug($chapterType),
                        'sort_order' => $order++,
                        'status' => 'empty',
                        'is_ai_generated' => false,
                    ]);
                }
            }
        }

        // Redirect to wizard steps
        return redirect()->route('wizard.steps', ['businessPlan' => $plan->id]);
    }

    protected function getChapterTitle($chapterType)
    {
        // Normalize chapter type (convert to slug if needed)
        $normalizedType = \Illuminate\Support\Str::slug($chapterType);

        $titles = [
            'executive-summary' => 'الملخص التنفيذي',
            'executive_summary' => 'الملخص التنفيذي',
            'company-description' => 'وصف الشركة',
            'company_description' => 'وصف الشركة',
            'menu-services' => 'قائمة المنتجات والخدمات',
            'menu_services' => 'قائمة المنتجات والخدمات',
            'market-analysis' => 'تحليل السوق',
            'market_analysis' => 'تحليل السوق',
            'marketing-strategy' => 'الاستراتيجية التسويقية',
            'marketing_strategy' => 'الاستراتيجية التسويقية',
            'financial-projections' => 'التوقعات المالية',
            'financial_projections' => 'التوقعات المالية',
            'operations-plan' => 'خطة العمليات',
            'operations_plan' => 'خطة العمليات',
            'products-catalog' => 'كتالوج المنتجات',
            'products_catalog' => 'كتالوج المنتجات',
            'product-technology' => 'المنتج والتكنولوجيا',
            'product-&-technology' => 'المنتج والتكنولوجيا',
            'target-audience' => 'الجمهور المستهدف',
            'target_audience' => 'الجمهور المستهدف',
            'marketing-digital-strategy' => 'استراتيجية التسويق الرقمي',
            'marketing_digital_strategy' => 'استراتيجية التسويق الرقمي',
            'logistics-shipping' => 'الخدمات اللوجستية والشحن',
            'logistics_shipping' => 'الخدمات اللوجستية والشحن',
            'technology-stack' => 'التقنيات المستخدمة',
            'technology_stack' => 'التقنيات المستخدمة',
            'problem-solution' => 'المشكلة والحل',
            'problem_solution' => 'المشكلة والحل',
            'product-description' => 'وصف المنتج',
            'product_description' => 'وصف المنتج',
            'market-opportunity' => 'الفرصة السوقية',
            'market_opportunity' => 'الفرصة السوقية',
            'business-model' => 'نموذج الأعمال',
            'business_model' => 'نموذج الأعمال',
            'competitive-analysis' => 'التحليل التنافسي',
            'competitive_analysis' => 'التحليل التنافسي',
            'team-structure' => 'هيكل الفريق',
            'team_structure' => 'هيكل الفريق',
            'funding-requirements' => 'متطلبات التمويل',
            'funding_requirements' => 'متطلبات التمويل',
        ];

        // Try normalized type first, then original
        return $titles[$normalizedType] ?? $titles[$chapterType] ?? $chapterType;
    }

    public function render()
    {
        return view('livewire.wizard-start');
    }
}
