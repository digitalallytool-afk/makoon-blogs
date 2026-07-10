<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoryCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the stories that belong to this category.
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'story_category_id');
    }
}
