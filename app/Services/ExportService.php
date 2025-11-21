<?php

namespace App\Services;

use App\Models\BusinessPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory as PresentationIOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessPlanExport;
use Illuminate\Support\Facades\Storage;
use App\Services\PdfExportService;

class ExportService
{
    /**
     * Export business plan to PDF
     */
    public function exportToPDF(BusinessPlan $plan): string
    {
        // Use mPDF service for better Arabic support
        $pdfService = new PdfExportService();
        return $pdfService->exportToPDF($plan);
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
     * Export business plan to PowerPoint (PPTX)
     */
    public function exportToPowerPoint(BusinessPlan $plan): string
    {
        $plan->load(['chapters', 'template', 'user']);

        // Create new PowerPoint presentation
        $presentation = new PhpPresentation();
        $presentation->getDocumentProperties()
            ->setCreator($plan->user->name)
            ->setCompany($plan->company_name)
            ->setTitle($plan->title)
            ->setDescription($plan->description)
            ->setCategory('Business Plan');

        // Remove default slide
        $presentation->removeSlideByIndex(0);

        // Slide 1: Title Slide
        $titleSlide = $presentation->createSlide();
        $titleSlide->setBackground()->setColor(new Color('FF1F4788'));

        $titleShape = $titleSlide->createRichTextShape()
            ->setHeight(200)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(150);
        $titleShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $titleText = $titleShape->createTextRun($plan->title);
        $titleText->getFont()->setBold(true)->setSize(44)->setColor(new Color('FFFFFFFF'));

        $companyShape = $titleSlide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(380);
        $companyShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $companyText = $companyShape->createTextRun($plan->company_name);
        $companyText->getFont()->setBold(true)->setSize(28)->setColor(new Color('FFFFFFFF'));

        // Slide 2: Overview
        $overviewSlide = $presentation->createSlide();
        $overviewSlide->setBackground()->setColor(new Color('FFF5F5F5'));

        $overviewTitle = $overviewSlide->createRichTextShape()
            ->setHeight(80)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(30);
        $overviewTitle->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $titleRun = $overviewTitle->createTextRun('نظرة عامة');
        $titleRun->getFont()->setBold(true)->setSize(32)->setColor(new Color('FF1F4788'));

        $overviewContent = $overviewSlide->createRichTextShape()
            ->setHeight(400)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(140);

        $content = "نوع المشروع: " . $this->getProjectTypeLabel($plan->project_type) . "\n\n";
        $content .= "نوع الصناعة: " . $plan->industry_type . "\n\n";
        $content .= "الحالة: " . $this->getStatusLabel($plan->status) . "\n\n";
        $content .= "نسبة الإكمال: " . $plan->completion_percentage . "%";

        $overviewContent->createTextRun($content)->getFont()->setSize(18);

        // Slide 3: Vision & Mission
        if ($plan->vision || $plan->mission) {
            $visionSlide = $presentation->createSlide();
            $visionSlide->setBackground()->setColor(new Color('FFFFFF'));

            $visionTitle = $visionSlide->createRichTextShape()
                ->setHeight(80)
                ->setWidth(900)
                ->setOffsetX(50)
                ->setOffsetY(30);
            $visionTitle->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $visionTitleRun = $visionTitle->createTextRun('الرؤية والرسالة');
            $visionTitleRun->getFont()->setBold(true)->setSize(32)->setColor(new Color('FF1F4788'));

            $visionContent = $visionSlide->createRichTextShape()
                ->setHeight(400)
                ->setWidth(900)
                ->setOffsetX(50)
                ->setOffsetY(140);

            $visionText = "";
            if ($plan->vision) {
                $visionText .= "الرؤية:\n" . $plan->vision . "\n\n";
            }
            if ($plan->mission) {
                $visionText .= "الرسالة:\n" . $plan->mission;
            }

            $visionContent->createTextRun($visionText)->getFont()->setSize(16);
        }

        // Add chapter slides
        foreach ($plan->chapters as $index => $chapter) {
            $chapterSlide = $presentation->createSlide();
            $chapterSlide->setBackground()->setColor(new Color('FFFFFF'));

            // Chapter title
            $chapterTitle = $chapterSlide->createRichTextShape()
                ->setHeight(80)
                ->setWidth(900)
                ->setOffsetX(50)
                ->setOffsetY(30);
            $chapterTitle->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $chapterTitleRun = $chapterTitle->createTextRun($chapter->title);
            $chapterTitleRun->getFont()->setBold(true)->setSize(28)->setColor(new Color('FF1F4788'));

            // Chapter content
            if ($chapter->content) {
                $chapterContent = $chapterSlide->createRichTextShape()
                    ->setHeight(420)
                    ->setWidth(900)
                    ->setOffsetX(50)
                    ->setOffsetY(130);

                $contentText = strip_tags($chapter->content);
                // Limit content to 500 characters for slide
                if (strlen($contentText) > 500) {
                    $contentText = substr($contentText, 0, 500) . '...';
                }

                $chapterContent->createTextRun($contentText)->getFont()->setSize(14);
            }
        }

        // Final Slide
        $finalSlide = $presentation->createSlide();
        $finalSlide->setBackground()->setColor(new Color('FF1F4788'));

        $finalShape = $finalSlide->createRichTextShape()
            ->setHeight(200)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(200);
        $finalShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $finalText = $finalShape->createTextRun('شكراً لكم');
        $finalText->getFont()->setBold(true)->setSize(48)->setColor(new Color('FFFFFFFF'));

        $dateShape = $finalSlide->createRichTextShape()
            ->setHeight(50)
            ->setWidth(900)
            ->setOffsetX(50)
            ->setOffsetY(420);
        $dateShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $dateText = $dateShape->createTextRun('تم إنشاؤها في: ' . now()->format('Y-m-d'));
        $dateText->getFont()->setSize(16)->setColor(new Color('FFFFFFFF'));

        // Save file
        $filename = $this->generateFilename($plan, 'pptx');
        $path = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $objWriter = PresentationIOFactory::createWriter($presentation, 'PowerPoint2007');
        $objWriter->save($path);

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
