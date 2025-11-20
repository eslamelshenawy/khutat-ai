<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get statistics
        $stats = [
            'total_plans' => $user->businessPlans()->count(),
            'draft_plans' => $user->businessPlans()->where('status', 'draft')->count(),
            'in_progress_plans' => $user->businessPlans()->where('status', 'in_progress')->count(),
            'completed_plans' => $user->businessPlans()->where('status', 'completed')->count(),
            'average_completion' => round($user->businessPlans()->avg('completion_percentage') ?? 0, 1),
        ];

        // Get recent plans
        $recentPlans = $user->businessPlans()
            ->with(['chapters', 'template'])
            ->latest()
            ->limit(5)
            ->get();

        // Get plans by status
        $plansByStatus = $user->businessPlans()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get plans by project type
        $plansByProjectType = $user->businessPlans()
            ->select('project_type', DB::raw('count(*) as count'))
            ->groupBy('project_type')
            ->pluck('count', 'project_type')
            ->toArray();

        // Get industry distribution
        $industryDistribution = $user->businessPlans()
            ->select('industry_type', DB::raw('count(*) as count'))
            ->groupBy('industry_type')
            ->pluck('count', 'industry_type')
            ->toArray();

        // Get AI generations count
        $aiGenerationsCount = $user->aiGenerations()->count();

        // Get chat messages count
        $chatMessagesCount = $user->chatMessages()->count();

        // Recent activity (last 7 days)
        $recentActivity = $user->businessPlans()
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'status', 'updated_at']);

        return view('dashboard.index', compact(
            'stats',
            'recentPlans',
            'plansByStatus',
            'plansByProjectType',
            'industryDistribution',
            'aiGenerationsCount',
            'chatMessagesCount',
            'recentActivity'
        ));
    }

    public function analytics()
    {
        $user = auth()->user();

        // Monthly plans creation
        $monthlyPlans = $user->businessPlans()
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Completion progress over time
        $completionProgress = $user->businessPlans()
            ->select('id', 'title', 'created_at', 'completion_percentage', 'status')
            ->orderBy('completion_percentage', 'desc')
            ->get();

        // AI usage statistics
        $aiUsageStats = [
            'total_generations' => $user->aiGenerations()->count(),
            'successful_generations' => $user->aiGenerations()->where('status', 'completed')->count(),
            'failed_generations' => $user->aiGenerations()->where('status', 'failed')->count(),
            'total_tokens_used' => $user->aiGenerations()->sum('tokens_used'),
        ];

        return view('dashboard.analytics', compact(
            'monthlyPlans',
            'completionProgress',
            'aiUsageStats'
        ));
    }
}
