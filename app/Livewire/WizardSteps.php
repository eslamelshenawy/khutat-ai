<?php

namespace App\Livewire;

use App\Models\BusinessPlan;
use App\Services\OllamaService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
#[Title('معالج إنشاء خطة العمل')]
class WizardSteps extends Component
{
    public BusinessPlan $plan;
    public $currentChapterId = null;
    public $currentChapter = null;
    public $chapters = [];
    public $showAIGenerator = false;
    public $aiGenerating = false;

    public function mount($businessPlan)
    {
        $this->plan = BusinessPlan::with('chapters')->findOrFail($businessPlan);

        // Check authorization
        Gate::authorize('view', $this->plan);

        $this->chapters = $this->plan->chapters()->orderBy('sort_order')->get();

        // Set first chapter as current if exists
        if ($this->chapters->count() > 0 && !$this->currentChapterId) {
            $chapter = $this->chapters->first();
            $this->currentChapterId = $chapter->id;
            $this->currentChapter = [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'content' => $chapter->content,
                'status' => $chapter->status,
                'is_ai_generated' => $chapter->is_ai_generated,
                'description' => null,
            ];
        }
    }

    public function selectChapter($chapterId)
    {
        $chapter = $this->plan->chapters()->find($chapterId);

        if ($chapter) {
            $this->currentChapterId = $chapterId;
            $this->currentChapter = [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'content' => $chapter->content,
                'status' => $chapter->status,
                'is_ai_generated' => $chapter->is_ai_generated,
                'description' => null, // Chapter model doesn't have description field
            ];
        }
    }

    public function saveChapter()
    {
        Gate::authorize('update', $this->plan);

        $this->validate([
            'currentChapter.content' => 'nullable|string',
        ]);

        $chapter = $this->plan->chapters()->find($this->currentChapterId);

        if ($chapter) {
            $chapter->update([
                'content' => $this->currentChapter['content'] ?? '',
                'status' => empty($this->currentChapter['content']) ? 'empty' : 'draft',
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'تم حفظ الفصل بنجاح'
            ]);

            // Reload chapters
            $this->chapters = $this->plan->chapters()->orderBy('sort_order')->get();

            // Update current chapter as array
            $chapter = $chapter->fresh();
            $this->currentChapter = [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'content' => $chapter->content,
                'status' => $chapter->status,
                'is_ai_generated' => $chapter->is_ai_generated,
                'description' => null,
            ];

            // Update plan completion percentage
            $this->updateCompletionPercentage();
        }
    }

    public function generateWithAI()
    {
        Gate::authorize('update', $this->plan);

        $this->aiGenerating = true;

        try {
            $ollama = new OllamaService();
            $chapter = $this->plan->chapters()->find($this->currentChapterId);

            if ($chapter) {
                // Get additional context from business plan
                $context = [
                    'vision' => $this->plan->vision,
                    'mission' => $this->plan->mission,
                ];

                $content = $ollama->generateChapterContent($chapter, $context);

                $chapter->update([
                    'content' => $content,
                    'status' => 'ai_generated',
                    'is_ai_generated' => true,
                ]);

                // Reload chapter and update current chapter as array
                $chapter = $chapter->fresh();
                $this->currentChapter = [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'content' => $chapter->content,
                    'status' => $chapter->status,
                    'is_ai_generated' => $chapter->is_ai_generated,
                    'description' => null, // Chapter model doesn't have description field
                ];

                $this->chapters = $this->plan->chapters()->orderBy('sort_order')->get();

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'تم توليد المحتوى بواسطة الذكاء الاصطناعي'
                ]);

                // Update plan completion percentage
                $this->updateCompletionPercentage();
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        } finally {
            $this->aiGenerating = false;
        }
    }

    public function nextChapter()
    {
        // Save current chapter first
        $this->saveChapter();

        $currentIndex = $this->chapters->search(function ($chapter) {
            return $chapter->id == $this->currentChapterId;
        });

        if ($currentIndex !== false && $currentIndex < $this->chapters->count() - 1) {
            $nextChapter = $this->chapters[$currentIndex + 1];
            $this->selectChapter($nextChapter->id);
        }
    }

    public function previousChapter()
    {
        // Save current chapter first
        $this->saveChapter();

        $currentIndex = $this->chapters->search(function ($chapter) {
            return $chapter->id == $this->currentChapterId;
        });

        if ($currentIndex !== false && $currentIndex > 0) {
            $previousChapter = $this->chapters[$currentIndex - 1];
            $this->selectChapter($previousChapter->id);
        }
    }

    public function completePlan()
    {
        Gate::authorize('update', $this->plan);

        // Save current chapter
        $this->saveChapter();

        // Update plan status
        $this->plan->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'published_at' => now(),
        ]);

        return redirect()->route('business-plans.show', ['businessPlan' => $this->plan->id])->with('success', 'تم إكمال خطة العمل بنجاح!');
    }

    protected function updateCompletionPercentage()
    {
        $totalChapters = $this->plan->chapters()->count();

        if ($totalChapters > 0) {
            $completedChapters = $this->plan->chapters()
                ->whereIn('status', ['draft', 'ai_generated', 'completed'])
                ->where('content', '!=', '')
                ->whereNotNull('content')
                ->count();

            $percentage = round(($completedChapters / $totalChapters) * 100);

            $this->plan->update([
                'completion_percentage' => $percentage,
            ]);

            $this->plan->refresh();
        }
    }

    public function render()
    {
        return view('livewire.wizard-steps');
    }
}
