<?php

namespace App\Exports;

use App\Models\BusinessPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BusinessPlanExport implements WithMultipleSheets
{
    protected BusinessPlan $plan;

    public function __construct(BusinessPlan $plan)
    {
        $this->plan = $plan;
        $this->plan->load(['chapters', 'template', 'user']);
    }

    public function sheets(): array
    {
        return [
            new BusinessPlanOverviewSheet($this->plan),
            new BusinessPlanChaptersSheet($this->plan),
        ];
    }
}

class BusinessPlanOverviewSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected BusinessPlan $plan;

    public function __construct(BusinessPlan $plan)
    {
        $this->plan = $plan;
    }

    public function collection()
    {
        return collect([
            ['العنوان', $this->plan->title],
            ['اسم الشركة', $this->plan->company_name],
            ['نوع المشروع', $this->getProjectTypeLabel()],
            ['نوع الصناعة', $this->plan->industry_type],
            ['الحالة', $this->getStatusLabel()],
            ['نسبة الإكمال', $this->plan->completion_percentage . '%'],
            ['الرؤية', $this->plan->vision],
            ['الرسالة', $this->plan->mission],
            ['عام', $this->plan->is_public ? 'نعم' : 'لا'],
            ['السماح بالتعليقات', $this->plan->allow_comments ? 'نعم' : 'لا'],
            ['الإصدار', $this->plan->version],
            ['تاريخ الإنشاء', $this->plan->created_at->format('Y-m-d H:i')],
            ['آخر تحديث', $this->plan->updated_at->format('Y-m-d H:i')],
        ]);
    }

    public function headings(): array
    {
        return ['الحقل', 'القيمة'];
    }

    public function title(): string
    {
        return 'نظرة عامة';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A' => ['font' => ['bold' => true]],
        ];
    }

    protected function getProjectTypeLabel(): string
    {
        return match($this->plan->project_type) {
            'new_business' => 'مشروع جديد',
            'existing_expansion' => 'توسع مشروع قائم',
            'franchise' => 'فرنشايز',
            'startup' => 'شركة ناشئة',
            default => $this->plan->project_type,
        };
    }

    protected function getStatusLabel(): string
    {
        return match($this->plan->status) {
            'draft' => 'مسودة',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'مراجعة',
            'completed' => 'مكتمل',
            'archived' => 'مؤرشف',
            default => $this->plan->status,
        };
    }
}

class BusinessPlanChaptersSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithMapping
{
    protected BusinessPlan $plan;

    public function __construct(BusinessPlan $plan)
    {
        $this->plan = $plan;
    }

    public function collection()
    {
        return $this->plan->chapters;
    }

    public function map($chapter): array
    {
        return [
            $chapter->title,
            $chapter->chapter_type,
            $chapter->order_index,
            strip_tags($chapter->content ?? 'لم يتم إضافة محتوى'),
            strip_tags($chapter->ai_generated_content ?? ''),
            $chapter->is_completed ? 'مكتمل' : 'غير مكتمل',
            $chapter->word_count ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'العنوان',
            'نوع الفصل',
            'الترتيب',
            'المحتوى',
            'محتوى AI',
            'الحالة',
            'عدد الكلمات',
        ];
    }

    public function title(): string
    {
        return 'الفصول';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8F4F8']]],
        ];
    }
}
