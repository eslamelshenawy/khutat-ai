<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_plan_id',
        'user_id',
        'message',
        'is_user',
        'ai_model',
        'tokens_used',
        'processing_time_ms',
        'context',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'business_plan_id' => 'integer',
        'user_id' => 'integer',
        'is_user' => 'boolean',
        'tokens_used' => 'integer',
        'processing_time_ms' => 'integer',
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'sender_type',
    ];

    /**
     * Indicates if the model should be timestamped.
     * Only created_at is used, no updated_at.
     *
     * @var bool
     */
    public const UPDATED_AT = null;

    /**
     * Get the business plan that owns this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the user who sent this message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sender type (user or ai).
     *
     * @return string
     */
    public function getSenderTypeAttribute()
    {
        return $this->is_user ? 'user' : 'ai';
    }

    /**
     * Scope a query to only include user messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_user', true);
    }

    /**
     * Scope a query to only include AI messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAiMessages($query)
    {
        return $query->where('is_user', false);
    }

    /**
     * Scope a query to get conversation history (ordered by time).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConversation($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'asc')->limit($limit);
    }
}
