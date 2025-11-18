<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPlanData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_plan_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'field_key',
        'field_value',
        'field_type',
        'wizard_step',
        'is_validated',
        'validated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'wizard_step' => 'integer',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business plan that owns this data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the field value with proper type casting.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        $value = $this->field_value;

        return match ($this->field_type) {
            'number' => (float) $value,
            'json' => json_decode($value, true),
            'date' => \Carbon\Carbon::parse($value),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }

    /**
     * Set the field value with proper encoding.
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->attributes['field_value'] = match ($this->field_type) {
            'json' => json_encode($value),
            'date' => \Carbon\Carbon::parse($value)->toDateString(),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Scope a query to only include data for a specific wizard step.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $step
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('wizard_step', $step);
    }

    /**
     * Scope a query to only include validated data.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    /**
     * Scope a query to filter by field key.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('field_key', $key);
    }
}
