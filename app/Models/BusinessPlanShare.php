<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusinessPlanShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_plan_id',
        'shared_by',
        'token',
        'type',
        'password',
        'permission',
        'expires_at',
        'is_active',
        'view_count',
        'last_viewed_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_viewed_at' => 'datetime',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($share) {
            if (empty($share->token)) {
                $share->token = Str::random(32);
            }
        });
    }

    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function views()
    {
        return $this->hasMany(BusinessPlanShareView::class, 'share_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function incrementViewCount(array $metadata = []): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);

        $this->views()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'viewed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    public function getShareUrl(): string
    {
        return route('shared-plan.view', ['token' => $this->token]);
    }
}
