<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Story extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'story_category_id',
        'author_id',
        'view_count',
        'status',
        'updated_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    /**
     * Scope to only published stories.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Check if this story is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Get the story category that this story belongs to.
     */
    public function storyCategory(): BelongsTo
    {
        return $this->belongsTo(StoryCategory::class, 'story_category_id');
    }

    /**
     * Get the author that wrote this story.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * Get the user that last updated this story.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the story's canonical URL, cleaning any duplicate /blogs/ segments.
     */
    public function getCanonicalUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return str_replace('/blogs/blogs/', '/blogs/', $value);
    }
}
