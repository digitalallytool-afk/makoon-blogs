<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the video sessions for this category.
     */
    public function videoSessions(): HasMany
    {
        return $this->hasMany(VideoSession::class);
    }
}
