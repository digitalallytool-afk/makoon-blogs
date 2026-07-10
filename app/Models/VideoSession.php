<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoSession extends Model
{
    protected $fillable = [
        'session_category_id',
        'title',
        'slug',
        'description',
        'video_url',
        'image',
        'status',
    ];

    /**
     * Get the category that owns this video session.
     */
    public function sessionCategory(): BelongsTo
    {
        return $this->belongsTo(SessionCategory::class);
    }

    /**
     * Scope to only published video sessions.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Check if this session is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
