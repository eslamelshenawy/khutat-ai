<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show tasks for a business plan
     */
    public function index(BusinessPlan $businessPlan)
    {
        Gate::authorize('view', $businessPlan);

        $tasks = $businessPlan->tasks()
            ->with(['assignedTo', 'assignedBy'])
            ->latest()
            ->paginate(20);

        // Get team members (users who have shared access)
        $teamMembers = User::whereHas('businessPlans', function($query) use ($businessPlan) {
            $query->where('business_plans.id', $businessPlan->id);
        })->get();

        return view('business-plans.tasks.index', compact('businessPlan', 'tasks', 'teamMembers'));
    }

    /**
     * Store a new task
     */
    public function store(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:now',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['status'] = 'pending';

        $businessPlan->tasks()->create($validated);

        return back()->with('success', 'تم إنشاء المهمة بنجاح');
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, BusinessPlan $businessPlan, Task $task)
    {
        // Allow assigned user or plan owner to update status
        if ($task->assigned_to !== auth()->id() && !Gate::allows('update', $businessPlan)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('success', 'تم تحديث حالة المهمة');
    }

    /**
     * Update an existing task
     */
    public function update(Request $request, BusinessPlan $businessPlan, Task $task)
    {
        Gate::authorize('update', $businessPlan);

        if ($task->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return back()->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Delete a task
     */
    public function destroy(BusinessPlan $businessPlan, Task $task)
    {
        Gate::authorize('update', $businessPlan);

        if ($task->business_plan_id !== $businessPlan->id) {
            abort(404);
        }

        $task->delete();

        return back()->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Get my tasks (for current user)
     */
    public function myTasks(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = Task::where('assigned_to', auth()->id())
            ->with(['businessPlan', 'assignedBy'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tasks = $query->paginate(20);

        return view('tasks.my-tasks', compact('tasks', 'status'));
    }
}
