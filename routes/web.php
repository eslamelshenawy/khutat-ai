<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Wizard\WizardStart;
use App\Livewire\Wizard\WizardSteps;
use App\Livewire\Wizard\ChapterEditor;
use App\Http\Controllers\BusinessPlanController;

Route::get('/', function () {
    return view('welcome');
});

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
