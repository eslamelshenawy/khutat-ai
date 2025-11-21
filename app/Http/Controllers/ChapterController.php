<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ChapterController extends Controller
{
    /**
     * Update the order of chapters.
     */
    public function updateOrder(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|integer|exists:chapters,id',
            'chapters.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['chapters'] as $chapterData) {
            $chapter = Chapter::findOrFail($chapterData['id']);

            // Ensure the chapter belongs to this business plan
            if ($chapter->business_plan_id !== $businessPlan->id) {
                continue;
            }

            $chapter->update(['sort_order' => $chapterData['sort_order']]);
        }

        Log::info('Chapters reordered', [
            'plan_id' => $businessPlan->id,
            'chapters' => $validated['chapters'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب الفصول بنجاح'
        ]);
    }

    /**
     * Delete a chapter.
     */
    public function destroy(BusinessPlan $businessPlan, Chapter $chapter)
    {
        Gate::authorize('update', $businessPlan);

        if ($chapter->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        $chapter->delete();

        Log::info('Chapter deleted', [
            'plan_id' => $businessPlan->id,
            'chapter_id' => $chapter->id,
        ]);

        return redirect()->back()->with('success', 'تم حذف الفصل بنجاح');
    }
}
