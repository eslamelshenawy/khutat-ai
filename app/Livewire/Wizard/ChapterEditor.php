<?php

namespace App\Livewire\Wizard;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use App\Services\OllamaService;
use Livewire\Component;

class ChapterEditor extends Component
{
    public $businessPlan;
    public $chapters;
    public $currentChapter;
    public $content = '';
    public $aiGenerating = false;
    public $chatMessages = [];
    public $chatInput = '';

    protected $ollamaService;

    public function boot(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    public function mount($businessPlan)
    {
        $this->businessPlan = BusinessPlan::with('chapters')->findOrFail($businessPlan);
        $this->chapters = $this->businessPlan->chapters()->orderBy('sort_order')->get();

        if ($this->chapters->isNotEmpty()) {
            $this->selectChapter($this->chapters->first()->id);
        }
    }

    public function selectChapter($chapterId)
    {
        $this->currentChapter = Chapter::findOrFail($chapterId);
        $this->content = $this->currentChapter->content;
    }

    public function saveChapter()
    {
        $this->validate([
            'content' => 'required|min:10',
        ]);

        $this->currentChapter->update([
            'content' => $this->content,
            'word_count' => str_word_count(strip_tags($this->content)),
        ]);

        $this->dispatch('notify', ['message' => 'تم حفظ الفصل بنجاح', 'type' => 'success']);
    }

    public function generateWithAI()
    {
        $this->aiGenerating = true;

        try {
            // Get wizard data for context
            // Try new wizard_data field first, fallback to old businessPlanData
            if (!empty($this->businessPlan->wizard_data)) {
                $wizardData = $this->businessPlan->wizard_data;
            } elseif ($this->businessPlan->businessPlanData) {
                $wizardData = $this->businessPlan->businessPlanData->pluck('field_value', 'field_key')->toArray();
            } else {
                $wizardData = [];
            }

            // Generate content
            $generatedContent = $this->ollamaService->generateChapterContent($this->currentChapter, $wizardData);

            // Update chapter in database
            $this->currentChapter->update([
                'content' => $generatedContent,
                'is_ai_generated' => true,
                'word_count' => str_word_count(strip_tags($generatedContent)),
            ]);

            // Refresh the chapter model to get the saved content
            $this->currentChapter->refresh();

            // Update local content property from the refreshed chapter
            $this->content = $this->currentChapter->content;

            // Force Livewire to update the component by resetting the property
            $this->reset('chatInput');

            // Dispatch browser event to update textarea
            $this->dispatch('content-updated', content: $this->content);

            $this->dispatch('notify', ['message' => 'تم إنشاء المحتوى بالذكاء الاصطناعي', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'حدث خطأ: ' . $e->getMessage(), 'type' => 'error']);
        } finally {
            $this->aiGenerating = false;
        }
    }

    public function improveContent()
    {
        // Check if content is not empty
        if (empty($this->content)) {
            $this->dispatch('notify', ['message' => 'لا يوجد محتوى لتحسينه', 'type' => 'warning']);
            return;
        }

        $this->aiGenerating = true;

        try {
            $improvedContent = $this->ollamaService->improveContent($this->content, 'حسن هذا المحتوى وجعله أكثر احترافية');

            $this->content = $improvedContent;

            $this->dispatch('notify', ['message' => 'تم تحسين المحتوى بنجاح', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'حدث خطأ: ' . $e->getMessage(), 'type' => 'error']);
        } finally {
            $this->aiGenerating = false;
        }
    }

    public function sendChatMessage()
    {
        if (empty($this->chatInput)) {
            return;
        }

        $this->chatMessages[] = [
            'role' => 'user',
            'content' => $this->chatInput,
        ];

        try {
            $response = $this->ollamaService->chatWithAI($this->chatInput, [
                'business_plan' => $this->businessPlan->toArray(),
                'current_chapter' => $this->currentChapter->toArray(),
            ]);

            $this->chatMessages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
        } catch (\Exception $e) {
            $this->chatMessages[] = [
                'role' => 'assistant',
                'content' => 'عذراً، حدث خطأ في الاتصال بالذكاء الاصطناعي.',
            ];
        }

        $this->chatInput = '';
    }

    public function finishEditing()
    {
        $this->saveChapter();

        // Mark business plan as completed
        $this->businessPlan->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'published_at' => now(),
        ]);

        return redirect()->route('business-plans.show', ['businessPlan' => $this->businessPlan->id]);
    }

    public function render()
    {
        return view('livewire.wizard.chapter-editor');
    }
}
