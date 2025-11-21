<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class BusinessPlanTranslationController extends Controller
{
    protected $aiService;

    public function __construct(OllamaService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show translation options page
     */
    public function index(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        return view('business-plans.translate.index', compact('businessPlan'));
    }

    /**
     * Translate business plan to selected language
     */
    public function translate(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $validated = $request->validate([
            'target_language' => 'required|string|in:en,ar,fr,es,de,it,pt,ru,zh,ja,ko',
            'include_chapters' => 'boolean',
        ]);

        try {
            // Increase execution time limit
            set_time_limit(300); // 5 minutes

            $targetLanguage = $validated['target_language'];
            $includeChapters = $validated['include_chapters'] ?? true;

            // Get language name
            $languageNames = $this->getLanguageNames();
            $languageName = $languageNames[$targetLanguage] ?? $targetLanguage;

            // Translate basic info (only short texts)
            $translatedData = [
                'title' => $this->translateTextSimple($businessPlan->title, $targetLanguage),
                'description' => $businessPlan->description, // Keep original for now
                'company_name' => $businessPlan->company_name, // Keep original
                'vision' => $this->translateTextSimple($businessPlan->vision, $targetLanguage),
                'mission' => $this->translateTextSimple($businessPlan->mission, $targetLanguage),
                'target_market' => $businessPlan->target_market, // Keep original
            ];

            // Translate only chapter titles (not content to save time)
            $translatedChapters = [];
            if ($includeChapters && $businessPlan->chapters->count() > 0) {
                foreach ($businessPlan->chapters as $chapter) {
                    $translatedChapters[] = [
                        'title' => $this->translateTextSimple($chapter->title, $targetLanguage),
                        'content' => $chapter->content, // Keep original content
                        'sort_order' => $chapter->sort_order,
                    ];
                }
            }

            Log::info('Business plan translated', [
                'plan_id' => $businessPlan->id,
                'language' => $targetLanguage,
                'chapters_count' => count($translatedChapters),
            ]);

            return view('business-plans.translate.result', [
                'businessPlan' => $businessPlan,
                'translatedData' => $translatedData,
                'translatedChapters' => $translatedChapters,
                'targetLanguage' => $targetLanguage,
                'languageName' => $languageName,
            ]);

        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'فشل في ترجمة الخطة. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * Export translated plan
     */
    public function export(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $validated = $request->validate([
            'target_language' => 'required|string',
            'translated_data' => 'required|json',
            'translated_chapters' => 'nullable|json',
            'format' => 'required|string|in:pdf,word,text',
        ]);

        try {
            $translatedData = json_decode($validated['translated_data'], true);
            $translatedChapters = json_decode($validated['translated_chapters'] ?? '[]', true);
            $format = $validated['format'];

            // Create temporary translated copy
            $content = $this->generateTranslatedContent($translatedData, $translatedChapters);

            if ($format === 'pdf') {
                return $this->exportAsPdf($content, $translatedData['title']);
            } elseif ($format === 'word') {
                return $this->exportAsWord($content, $translatedData['title']);
            } else {
                return $this->exportAsText($content, $translatedData['title']);
            }

        } catch (\Exception $e) {
            Log::error('Translation export failed', [
                'plan_id' => $businessPlan->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'فشل في تصدير الخطة المترجمة.');
        }
    }

    /**
     * Simple translation using Google Translate
     */
    protected function translateTextSimple($text, $targetLanguage)
    {
        if (empty($text)) {
            return '';
        }

        try {
            // Use Google Translate library
            if (class_exists('\Stichoza\GoogleTranslate\GoogleTranslate')) {
                $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
                $tr->setSource('ar'); // Arabic source
                $tr->setTarget($targetLanguage);
                return $tr->translate($text);
            }
        } catch (\Exception $e) {
            \Log::warning('Google Translate failed', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 50),
            ]);
        }

        // Fallback to original text
        return $text;
    }

    /**
     * Translate text using AI service (slower)
     */
    protected function translateText($text, $targetLanguage)
    {
        if (empty($text)) {
            return '';
        }

        $languageNames = $this->getLanguageNames();
        $languageName = $languageNames[$targetLanguage] ?? $targetLanguage;

        $prompt = "Translate the following text to {$languageName}. Keep the same tone and style. Only return the translation without any explanations:\n\n{$text}";

        try {
            // Use improveContent method which uses generate internally
            $translation = $this->aiService->improveContent($text, "Translate to {$languageName}");
            return $translation;
        } catch (\Exception $e) {
            Log::warning('Translation fallback', [
                'error' => $e->getMessage(),
                'text_preview' => substr($text, 0, 100),
            ]);
            return $text; // Fallback to original text
        }
    }

    /**
     * Get supported language names
     */
    protected function getLanguageNames()
    {
        return [
            'en' => 'English',
            'ar' => 'Arabic',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
        ];
    }

    /**
     * Generate translated content for export
     */
    protected function generateTranslatedContent($translatedData, $translatedChapters)
    {
        $content = "# {$translatedData['title']}\n\n";
        $content .= "## Company: {$translatedData['company_name']}\n\n";

        if (!empty($translatedData['description'])) {
            $content .= "### Description\n{$translatedData['description']}\n\n";
        }

        if (!empty($translatedData['vision'])) {
            $content .= "### Vision\n{$translatedData['vision']}\n\n";
        }

        if (!empty($translatedData['mission'])) {
            $content .= "### Mission\n{$translatedData['mission']}\n\n";
        }

        if (!empty($translatedData['target_market'])) {
            $content .= "### Target Market\n{$translatedData['target_market']}\n\n";
        }

        foreach ($translatedChapters as $chapter) {
            $content .= "## {$chapter['title']}\n\n";
            $content .= "{$chapter['content']}\n\n";
        }

        return $content;
    }

    /**
     * Export as PDF
     */
    protected function exportAsPdf($content, $title)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML("<pre>{$content}</pre>");
        return $pdf->download(slug($title) . '_translated.pdf');
    }

    /**
     * Export as Word
     */
    protected function exportAsWord($content, $title)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText($content);

        $filename = slug($title) . '_translated.docx';
        $tempFile = storage_path('app/temp/' . $filename);

        if (!file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    /**
     * Export as Text
     */
    protected function exportAsText($content, $title)
    {
        $filename = slug($title) . '_translated.txt';
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
