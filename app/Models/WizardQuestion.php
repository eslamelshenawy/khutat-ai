<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WizardQuestion extends Model
{
    protected $fillable = [
        'wizard_step_id',
        'label',
        'help_text',
        'type',
        'options',
        'is_required',
        'order',
        'field_name',
        'validation_rules',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(WizardStep::class, 'wizard_step_id');
    }

    protected static function booted(): void
    {
        static::creating(function (WizardQuestion $question) {
            if (is_null($question->order)) {
                $question->order = static::where('wizard_step_id', $question->wizard_step_id)->max('order') + 1;
            }
        });
    }

    /**
     * Get validation rules for this question
     */
    public function getValidationRulesArray(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($this->type) {
            case 'text':
                $rules[] = 'string';
                $rules[] = 'max:500';
                break;
            case 'textarea':
                $rules[] = 'string';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'select':
            case 'radio':
                if ($this->options) {
                    $rules[] = 'in:' . implode(',', array_keys($this->options));
                }
                break;
            case 'checkbox':
                $rules[] = 'array';
                break;
        }

        // Merge with custom validation rules
        if ($this->validation_rules) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        return $rules;
    }
}
