<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\WizardStart;
use App\Livewire\WizardSteps;
use App\Livewire\Wizard\ChapterEditor;
use App\Http\Controllers\BusinessPlanController;

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

// Wizard Routes
Route::middleware(['auth'])->group(function () {
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
});
