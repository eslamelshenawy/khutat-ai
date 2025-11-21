<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPlanShareView extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'ip_address',
        'user_agent',
        'referer',
        'viewed_at',
        'metadata',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public $timestamps = false;

    public function share()
    {
        return $this->belongsTo(BusinessPlanShare::class, 'share_id');
    }
}
