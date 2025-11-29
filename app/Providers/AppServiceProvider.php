<?php

namespace App\Providers;

use App\Models\BusinessPlan;
use App\Models\WizardStep;
use App\Policies\BusinessPlanPolicy;
use App\Livewire\WizardQuestions;
use App\Livewire\WizardStart;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use LaraZeus\Bolt\Models\Form;
use LaraZeus\Bolt\Models\Field;
use LaraZeus\Bolt\Models\Section;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(BusinessPlan::class, BusinessPlanPolicy::class);

        // Auto-clear wizard cache when Bolt forms are updated
        $clearWizardCache = function () {
            WizardQuestions::clearStepsCache();
            WizardStart::clearTemplatesCache();
            Cache::forget('wizard_steps_v2');
            Cache::forget('wizard_templates_v2');
        };

        // Listen to Bolt Form changes
        Form::saved($clearWizardCache);
        Form::deleted($clearWizardCache);

        // Listen to Bolt Field changes
        Field::saved($clearWizardCache);
        Field::deleted($clearWizardCache);

        // Listen to Bolt Section changes
        Section::saved($clearWizardCache);
        Section::deleted($clearWizardCache);

        // Listen to WizardStep changes
        WizardStep::saved($clearWizardCache);
        WizardStep::deleted($clearWizardCache);
    }
}
