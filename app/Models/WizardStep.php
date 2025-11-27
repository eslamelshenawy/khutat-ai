<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaraZeus\Bolt\Models\Form as BoltForm;

class WizardStep extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'order',
        'is_active',
        'bolt_form_id',
        'enable_ai_suggestions',
        'ai_suggestion_prompt',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_ai_suggestions' => 'boolean',
        'bolt_form_id' => 'integer',
    ];

    /**
     * Get the Bolt form associated with this wizard step
     */
    public function boltForm(): BelongsTo
    {
        return $this->belongsTo(BoltForm::class, 'bolt_form_id');
    }

    /**
     * Get questions for this step (legacy - from wizard_questions table)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(WizardQuestion::class)->orderBy('order');
    }

    /**
     * Get active questions for this step (legacy)
     */
    public function activeQuestions(): HasMany
    {
        return $this->hasMany(WizardQuestion::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Check if this step uses Bolt form
     */
    public function usesBoltForm(): bool
    {
        return !is_null($this->bolt_form_id);
    }

    /**
     * Get Bolt form fields/sections for this step
     */
    public function getBoltFormFields()
    {
        if (!$this->usesBoltForm()) {
            return collect();
        }

        \$form = \$this->boltForm()->with(['sections.fields'])->first();

        if (!\$form) {
            return collect();
        }

        return \$form->sections;
    }

    protected static function booted(): void
    {
        static::creating(function (WizardStep \$step) {
            if (is_null(\$step->order)) {
                \$step->order = static::max('order') + 1;
            }
        });
    }
}
