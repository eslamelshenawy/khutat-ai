<?php

namespace App\Jobs;

use App\Models\BusinessPlan;
use App\Services\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeBusinessPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BusinessPlan $businessPlan
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(OllamaService $ollamaService): void
    {
        try {
            Log::info('Starting background analysis', [
                'plan_id' => $this->businessPlan->id,
            ]);

            $this->businessPlan->load(['chapters', 'template']);

            // Use OllamaService's analyzePlanQuality method
            $analysis = $ollamaService->analyzePlanQuality($this->businessPlan);

            // Update business plan with AI analysis
            $this->businessPlan->update([
                'ai_score' => $analysis['score'] ?? null,
                'ai_feedback' => $analysis['feedback'] ?? 'تم تحليل الخطة بنجاح',
            ]);

            Log::info('Background analysis completed successfully', [
                'plan_id' => $this->businessPlan->id,
                'score' => $analysis['score'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Background analysis failed', [
                'plan_id' => $this->businessPlan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update plan with error status
            $this->businessPlan->update([
                'ai_feedback' => 'حدث خطأ أثناء التحليل: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
