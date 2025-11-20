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
                $order = 1;
                foreach ($template->structure as $chapterType) {
                    $plan->chapters()->create([
                        'title' => $this->getChapterTitle($chapterType),
                        'slug' => \Illuminate\Support\Str::slug($chapterType . '-' . $plan->id),
                        'chapter_type' => $chapterType,
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
        $titles = [
            'executive_summary' => 'الملخص التنفيذي',
            'company_description' => 'وصف الشركة',
            'menu_services' => 'قائمة المنتجات والخدمات',
            'market_analysis' => 'تحليل السوق',
            'marketing_strategy' => 'الاستراتيجية التسويقية',
            'financial_projections' => 'التوقعات المالية',
            'operations_plan' => 'خطة العمليات',
            'products_catalog' => 'كتالوج المنتجات',
            'target_audience' => 'الجمهور المستهدف',
            'marketing_digital_strategy' => 'استراتيجية التسويق الرقمي',
            'logistics_shipping' => 'الخدمات اللوجستية والشحن',
            'technology_stack' => 'التقنيات المستخدمة',
            'problem_solution' => 'المشكلة والحل',
            'product_description' => 'وصف المنتج',
            'market_opportunity' => 'الفرصة السوقية',
            'business_model' => 'نموذج الأعمال',
            'competitive_analysis' => 'التحليل التنافسي',
            'team_structure' => 'هيكل الفريق',
            'funding_requirements' => 'متطلبات التمويل',
            'courses_programs' => 'الدورات والبرامج',
            'target_students' => 'الطلاب المستهدفون',
            'instructors_team' => 'فريق المدربين',
            'facilities_equipment' => 'المرافق والمعدات',
            'services_offered' => 'الخدمات المقدمة',
            'target_market' => 'السوق المستهدف',
            'location_setup' => 'الموقع والإعداد',
            'pricing_strategy' => 'استراتيجية التسعير',
            'marketing_plan' => 'خطة التسويق',
            'financial_plan' => 'الخطة المالية',
            'expertise_services' => 'الخدمات الاستشارية',
            'target_clients' => 'العملاء المستهدفون',
            'service_delivery' => 'تقديم الخدمة',
            'team_qualifications' => 'مؤهلات الفريق',
            'pricing_model' => 'نموذج التسعير',
            'implementation_timeline' => 'الجدول الزمني للتنفيذ',
            'risk_analysis' => 'تحليل المخاطر',
        ];

        return $titles[$chapterType] ?? ucfirst(str_replace('_', ' ', $chapterType));
    }

    public function render()
    {
        return view('livewire.wizard-start');
    }
}
