<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\WizardStart;
use App\Livewire\WizardSteps;
use App\Livewire\Wizard\ChapterEditor;
use App\Http\Controllers\BusinessPlanController;
use App\Http\Controllers\BusinessPlanShareController;
use App\Http\Controllers\PlanVersionController;
use App\Http\Controllers\ChapterController;

Route::get('/', function () {
    return view('welcome');
});

// Test CSS Route
Route::get('/test-css', function () {
    return view('test-css');
});

// Auth Routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/admin/register');
})->name('register');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// User Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [App\Http\Controllers\DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Wizard Routes
    Route::get('/wizard/start', WizardStart::class)->name('wizard.start');
    Route::get('/wizard/{businessPlan}/steps', WizardSteps::class)->name('wizard.steps');
    Route::get('/wizard/{businessPlan}/chapters', ChapterEditor::class)->name('chapters.edit');

    // Business Plan Routes
    Route::get('/plans', [BusinessPlanController::class, 'index'])->name('business-plans.index');
    Route::get('/plans/{businessPlan}', [BusinessPlanController::class, 'show'])->name('business-plans.show');
    Route::get('/plans/{businessPlan}/edit', [BusinessPlanController::class, 'edit'])->name('business-plans.edit');
    Route::delete('/plans/{businessPlan}', [BusinessPlanController::class, 'destroy'])->name('business-plans.destroy');
    Route::post('/plans/{businessPlan}/duplicate', [BusinessPlanController::class, 'duplicate'])->name('business-plans.duplicate');
    Route::get('/plans/{businessPlan}/export/{format}', [BusinessPlanController::class, 'export'])->name('business-plans.export');
    Route::post('/plans/{businessPlan}/analyze', [BusinessPlanController::class, 'analyze'])->name('business-plans.analyze');
    Route::post('/plans/{businessPlan}/recommendations', [BusinessPlanController::class, 'recommendations'])->name('business-plans.recommendations');

    // Share Links Routes
    Route::prefix('plans/{businessPlan}/share')->group(function () {
        Route::get('/create', [BusinessPlanShareController::class, 'create'])->name('business-plans.share.create');
        Route::post('/', [BusinessPlanShareController::class, 'store'])->name('business-plans.share.store');
        Route::delete('/{share}/deactivate', [BusinessPlanShareController::class, 'deactivate'])->name('business-plans.share.deactivate');
        Route::get('/{share}/analytics', [BusinessPlanShareController::class, 'analytics'])->name('business-plans.share.analytics');
        Route::post('/{share}/email', [BusinessPlanShareController::class, 'sendEmail'])->name('business-plans.share.email');
    });

    // Version History Routes
    Route::prefix('plans/{businessPlan}/versions')->group(function () {
        Route::get('/', [PlanVersionController::class, 'index'])->name('business-plans.versions.index');
        Route::post('/', [PlanVersionController::class, 'store'])->name('business-plans.versions.store');
        Route::get('/{version}', [PlanVersionController::class, 'show'])->name('business-plans.versions.show');
        Route::post('/{version}/restore', [PlanVersionController::class, 'restore'])->name('business-plans.versions.restore');
        Route::delete('/{version}', [PlanVersionController::class, 'destroy'])->name('business-plans.versions.destroy');
        Route::get('/{version1}/compare/{version2}', [PlanVersionController::class, 'compare'])->name('business-plans.versions.compare');
    });

    // Chapter Management Routes
    Route::prefix('plans/{businessPlan}/chapters')->group(function () {
        Route::post('/reorder', [ChapterController::class, 'updateOrder'])->name('business-plans.chapters.reorder');
        Route::delete('/{chapter}', [ChapterController::class, 'destroy'])->name('business-plans.chapters.destroy');
    });

    // Export Routes (specific routes for each format)
    Route::get('/plans/{businessPlan}/export-pdf', [App\Http\Controllers\BusinessPlanExportController::class, 'exportPdf'])->name('business-plans.export-pdf');
    Route::get('/plans/{businessPlan}/export-word', [App\Http\Controllers\BusinessPlanExportController::class, 'exportWord'])->name('business-plans.export-word');
    Route::get('/plans/{businessPlan}/export-excel', [App\Http\Controllers\BusinessPlanExportController::class, 'exportExcel'])->name('business-plans.export-excel');

    // AI Chat Routes
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/history', [App\Http\Controllers\ChatController::class, 'history'])->name('chat.history');

    // Notification Routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-all-read', [App\Http\Controllers\NotificationController::class, 'deleteAllRead'])->name('notifications.delete-all-read');
});

// Public Share View Routes (outside auth middleware)
Route::get('/shared/{token}', [BusinessPlanShareController::class, 'view'])->name('shared-plan.view');
Route::post('/shared/{token}/authenticate', [BusinessPlanShareController::class, 'authenticate'])->name('shared-plan.authenticate');
