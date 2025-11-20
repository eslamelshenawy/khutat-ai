<?php

namespace App\Services;

use App\Models\BusinessPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessPlanExport;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Export business plan to PDF
     */
    public function exportToPDF(BusinessPlan $plan): string
    {
        // Load the business plan with all relationships
        $plan->load(['chapters', 'template', 'user']);

        // Generate PDF with Arabic support
        $pdf = Pdf::loadView('exports.business-plan-pdf', [
            'plan' => $plan,
        ])->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true)
          ->setOption('defaultFont', 'dejavu sans')
          ->setOption('fontHeightRatio', 1.1)
          ->setOption('isPhpEnabled', false)
          ->setOption('isFontSubsettingEnabled', true);

        // Generate filename
        $filename = $this->generateFilename($plan, 'pdf');

        // Save to storage
        $path = 'exports/' . $filename;
        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Export business plan to Word (DOCX)
     */
    public function exportToWord(BusinessPlan $plan): string
    {
        $plan->load(['chapters', 'template', 'user']);

        // Create new Word document
        $phpWord = new PhpWord();

        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($plan->user->name);
        $properties->setCompany($plan->company_name);
        $properties->setTitle($plan->title);
        $properties->setDescription($plan->description);
        $properties->setCategory('Business Plan');

        // Add title page
        $section = $phpWord->addSection();

        // Title
        $titleStyle = [
            'name' => 'Arial',
            'size' => 24,
            'bold' => true,
            'color' => '1F4788'
        ];
        $section->addText($plan->title, $titleStyle, ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Company name
        $companyStyle = ['name' => 'Arial', 'size' => 18, 'bold' => true];
        $section->addText($plan->company_name, $companyStyle, ['alignment' => 'center']);
        $section->addTextBreak(2);

        // Business Plan Details
        $detailsStyle = ['name' => 'Arial', 'size' => 12];
        $section->addText('نوع المشروع: ' . $this->getProjectTypeLabel($plan->project_type), $detailsStyle);
        $section->addText('نوع الصناعة: ' . $plan->industry_type, $detailsStyle);
        $section->addText('الحالة: ' . $this->getStatusLabel($plan->status), $detailsStyle);
        $section->addText('نسبة الإكمال: ' . $plan->completion_percentage . '%', $detailsStyle);
        $section->addTextBreak(1);

        // Vision & Mission
        if ($plan->vision) {
            $section->addText('الرؤية:', ['bold' => true, 'size' => 14]);
            $section->addText($plan->vision, $detailsStyle);
            $section->addTextBreak(1);
        }

        if ($plan->mission) {
            $section->addText('الرسالة:', ['bold' => true, 'size' => 14]);
            $section->addText($plan->mission, $detailsStyle);
            $section->addTextBreak(2);
        }

        // Add chapters
        foreach ($plan->chapters as $chapter) {
            $section->addPageBreak();

            // Chapter title
            $section->addText(
                $chapter->title,
                ['name' => 'Arial', 'size' => 18, 'bold' => true, 'color' => '1F4788']
            );
            $section->addTextBreak(1);

            // Chapter content
            if ($chapter->content) {
                $section->addText(
                    strip_tags($chapter->content),
                    ['name' => 'Arial', 'size' => 12]
                );
            }

            // AI Generated content
            if ($chapter->ai_generated_content) {
                $section->addTextBreak(1);
                $section->addText('محتوى تم توليده بالذكاء الاصطناعي:', ['bold' => true, 'italic' => true, 'size' => 11]);
                $section->addText(
                    strip_tags($chapter->ai_generated_content),
                    ['name' => 'Arial', 'size' => 11, 'color' => '666666']
                );
            }
        }

        // Add footer
        $section->addPageBreak();
        $section->addText(
            'تم إنشاء هذه الخطة بواسطة Business Plan Wizard',
            ['name' => 'Arial', 'size' => 10, 'italic' => true],
            ['alignment' => 'center']
        );
        $section->addText(
            'تاريخ الإنشاء: ' . now()->format('Y-m-d'),
            ['name' => 'Arial', 'size' => 10],
            ['alignment' => 'center']
        );

        // Save file
        $filename = $this->generateFilename($plan, 'docx');
        $path = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($path);

        return 'exports/' . $filename;
    }

    /**
     * Export business plan to Excel
     */
    public function exportToExcel(BusinessPlan $plan): string
    {
        $filename = $this->generateFilename($plan, 'xlsx');

        Excel::store(
            new BusinessPlanExport($plan),
            'exports/' . $filename
        );

        return 'exports/' . $filename;
    }

    /**
     * Generate filename for export
     */
    protected function generateFilename(BusinessPlan $plan, string $extension): string
    {
        $slug = \Illuminate\Support\Str::slug($plan->title);
        $timestamp = now()->format('Y-m-d_His');

        return "{$slug}_{$timestamp}.{$extension}";
    }

    /**
     * Get project type label
     */
    protected function getProjectTypeLabel(string $type): string
    {
        return match($type) {
            'new_business' => 'مشروع جديد',
            'existing_expansion' => 'توسع مشروع قائم',
            'franchise' => 'فرنشايز',
            'startup' => 'شركة ناشئة',
            default => $type,
        };
    }

    /**
     * Get status label
     */
    protected function getStatusLabel(string $status): string
    {
        return match($status) {
            'draft' => 'مسودة',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'مراجعة',
            'completed' => 'مكتمل',
            'archived' => 'مؤرشف',
            default => $status,
        };
    }
}
