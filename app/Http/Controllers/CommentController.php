<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $businessPlan->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'تم إضافة التعليق بنجاح');
    }

    /**
     * Update a comment
     */
    public function update(Request $request, BusinessPlan $businessPlan, Comment $comment)
    {
        // Verify comment belongs to the business plan
        if ($comment->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        Gate::authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment->update($validated);

        return back()->with('success', 'تم تحديث التعليق بنجاح');
    }

    /**
     * Delete a comment
     */
    public function destroy(BusinessPlan $businessPlan, Comment $comment)
    {
        // Verify comment belongs to the business plan
        if ($comment->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        Gate::authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'تم حذف التعليق بنجاح');
    }

    /**
     * Mark comment as resolved
     */
    public function resolve(BusinessPlan $businessPlan, Comment $comment)
    {
        // Verify comment belongs to the business plan
        if ($comment->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        Gate::authorize('update', $comment);

        $comment->update(['is_resolved' => !$comment->is_resolved]);

        $message = $comment->is_resolved ? 'تم وضع علامة محلول' : 'تم إلغاء علامة محلول';

        return back()->with('success', $message);
    }
}
