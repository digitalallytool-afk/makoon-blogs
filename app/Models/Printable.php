<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printable extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'file_path',
        'file_name',
        'file_size',
        'download_count',
        'status',
        'updated_by',
    ];

    /**
     * Scope to only published printables.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Check if this printable is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Get the user that last updated this printable.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
