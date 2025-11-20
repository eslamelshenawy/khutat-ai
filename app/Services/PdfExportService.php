<?php

namespace App\Services;

use App\Models\BusinessPlan;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;

class PdfExportService
{
    /**
     * Export business plan to PDF using mPDF with proper Arabic support
     */
    public function exportToPDF(BusinessPlan $plan): string
    {
        // Load the business plan with all relationships
        $plan->load(['chapters', 'template', 'user']);

        // Create mPDF instance with Arabic support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'default_font_size' => 14,
            'default_font' => 'dejavusans',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        // Set document properties
        $mpdf->SetTitle($plan->title);
        $mpdf->SetAuthor($plan->user->name);
        $mpdf->SetCreator('Business Plan Wizard');

        // Build HTML content
        $html = $this->buildHtmlContent($plan);

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Generate filename
        $filename = $this->generateFilename($plan);
        $path = 'exports/' . $filename;

        // Ensure directory exists
        $fullPath = storage_path('app/' . $path);
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save PDF
        $mpdf->Output($fullPath, \Mpdf\Output\Destination::FILE);

        return $path;
    }

    /**
     * Build HTML content for PDF
     */
    protected function buildHtmlContent(BusinessPlan $plan): string
    {
        $html = '<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            direction: rtl;
            text-align: right;
            color: #333;
            line-height: 1.8;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1F4788;
            padding-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1F4788;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .details {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .details-row {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #1F4788;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1F4788;
            border-bottom: 2px solid #1F4788;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .section-content {
            text-align: justify;
            line-height: 1.8;
        }
        .chapter {
            page-break-before: always;
            margin: 30px 0;
        }
        .chapter-title {
            font-size: 20px;
            font-weight: bold;
            color: #1F4788;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F4788;
            padding-bottom: 10px;
        }
        .chapter-content {
            text-align: justify;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .ai-content {
            background: #f9f9f9;
            border-right: 4px solid #4CAF50;
            padding: 15px;
            margin: 15px 0;
        }
        .ai-label {
            font-size: 11px;
            font-style: italic;
            color: #666;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #1F4788;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<div class="title">' . htmlspecialchars($plan->title) . '</div>';
        $html .= '<div class="company-name">' . htmlspecialchars($plan->company_name) . '</div>';
        $html .= '</div>';

        // Details
        $html .= '<div class="details">';
        $html .= '<div class="details-row"><span class="label">نوع المشروع:</span> ' . $this->getProjectTypeLabel($plan->project_type) . '</div>';
        $html .= '<div class="details-row"><span class="label">نوع الصناعة:</span> ' . htmlspecialchars($plan->industry_type) . '</div>';
        $html .= '<div class="details-row"><span class="label">الحالة:</span> ' . $this->getStatusLabel($plan->status) . '</div>';
        $html .= '<div class="details-row"><span class="label">نسبة الإكمال:</span> ' . $plan->completion_percentage . '%</div>';
        $html .= '<div class="details-row"><span class="label">تاريخ الإنشاء:</span> ' . $plan->created_at->format('Y-m-d') . '</div>';
        $html .= '</div>';

        // Vision & Mission
        if ($plan->vision) {
            $html .= '<div class="section">';
            $html .= '<div class="section-title">الرؤية</div>';
            $html .= '<div class="section-content">' . nl2br(htmlspecialchars($plan->vision)) . '</div>';
            $html .= '</div>';
        }

        if ($plan->mission) {
            $html .= '<div class="section">';
            $html .= '<div class="section-title">الرسالة</div>';
            $html .= '<div class="section-content">' . nl2br(htmlspecialchars($plan->mission)) . '</div>';
            $html .= '</div>';
        }

        // Chapters
        foreach ($plan->chapters as $chapter) {
            $html .= '<div class="chapter">';
            $html .= '<div class="chapter-title">' . htmlspecialchars($chapter->title) . '</div>';

            if ($chapter->content) {
                $html .= '<div class="chapter-content">' . nl2br(htmlspecialchars($chapter->content)) . '</div>';
            }

            if ($chapter->ai_generated_content) {
                $html .= '<div class="ai-content">';
                $html .= '<div class="ai-label">محتوى تم توليده بالذكاء الاصطناعي:</div>';
                $html .= nl2br(htmlspecialchars($chapter->ai_generated_content));
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>تم إنشاء هذه الخطة بواسطة <strong>Business Plan Wizard</strong></p>';
        $html .= '<p>تاريخ الإنشاء: ' . now()->format('Y-m-d H:i') . '</p>';
        $html .= '</div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate filename for export
     */
    protected function generateFilename(BusinessPlan $plan): string
    {
        $slug = \Illuminate\Support\Str::slug($plan->title);
        $timestamp = now()->format('Y-m-d_His');

        return "{$slug}_{$timestamp}.pdf";
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
            'general' => 'عام',
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
