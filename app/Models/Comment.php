<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_plan_id',
        'user_id',
        'parent_id',
        'content',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    /**
     * Get the business plan that owns the comment
     */
    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class);
    }

    /**
     * Get the user that wrote the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for threaded replies)
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
