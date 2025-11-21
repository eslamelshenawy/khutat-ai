<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Services\ExportService;
use Illuminate\Http\Request;

class BusinessPlanExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export business plan to PDF
     */
    public function exportPdf(BusinessPlan $businessPlan)
    {
        // Check authorization
        $this->authorize('view', $businessPlan);

        try {
            $path = $this->exportService->exportToPDF($businessPlan);
            $fullPath = storage_path('app/' . $path);

            if (!file_exists($fullPath)) {
                abort(404, 'PDF file not found');
            }

            return response()->download($fullPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'خطأ في تصدير PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export business plan to Word
     */
    public function exportWord(BusinessPlan $businessPlan)
    {
        // Check authorization
        $this->authorize('view', $businessPlan);

        try {
            $path = $this->exportService->exportToWord($businessPlan);
            $fullPath = storage_path('app/' . $path);

            if (!file_exists($fullPath)) {
                abort(404, 'Word file not found');
            }

            return response()->download($fullPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Word Export Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'خطأ في تصدير Word: ' . $e->getMessage());
        }
    }

    /**
     * Export business plan to Excel
     */
    public function exportExcel(BusinessPlan $businessPlan)
    {
        // Check authorization
        $this->authorize('view', $businessPlan);

        try {
            $path = $this->exportService->exportToExcel($businessPlan);
            $fullPath = storage_path('app/' . $path);

            if (!file_exists($fullPath)) {
                abort(404, 'Excel file not found');
            }

            return response()->download($fullPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Excel Export Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'خطأ في تصدير Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export business plan to PowerPoint
     */
    public function exportPowerPoint(BusinessPlan $businessPlan)
    {
        // Check authorization
        $this->authorize('view', $businessPlan);

        try {
            $path = $this->exportService->exportToPowerPoint($businessPlan);
            $fullPath = storage_path('app/' . $path);

            if (!file_exists($fullPath)) {
                abort(404, 'PowerPoint file not found');
            }

            return response()->download($fullPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('PowerPoint Export Error', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'خطأ في تصدير PowerPoint: ' . $e->getMessage());
        }
    }
}
