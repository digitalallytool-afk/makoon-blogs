<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category_id',
        'author_id',
        'view_count',
        'status',
        'is_selected',
        'is_trending',
        'updated_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_selected' => 'boolean',
            'is_trending' => 'boolean',
        ];
    }

    /**
     * Scope to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Check if this post is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Get the category that this post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the author that wrote this post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * Get the user that last updated this post.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
