<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Adds "publish later" support on top of the existing published/draft status.
 *
 * A record with status "published" and a future published_at is "scheduled":
 * it stays hidden from the public site until that moment passes, without
 * needing a cron job to flip any flag.
 */
trait HasScheduledPublishing
{
    public function initializeHasScheduledPublishing(): void
    {
        $this->mergeFillable(['published_at']);
        $this->mergeCasts(['published_at' => 'datetime']);
    }

    /**
     * Scope to records that are live on the public site right now.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope to published records waiting for their scheduled time.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '>', now());
    }

    /**
     * Whether the record is waiting for its scheduled publish time.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isFuture();
    }

    /**
     * Whether the record is live on the public site right now.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && ! $this->isScheduled();
    }

    /**
     * Scheduled time in the editors' timezone, formatted for a datetime-local input.
     */
    public function scheduledAtForInput(): ?string
    {
        if (! $this->isScheduled()) {
            return null;
        }

        return $this->published_at->copy()->timezone(config('app.schedule_timezone'))->format('Y-m-d\TH:i');
    }

    /**
     * Scheduled time in the editors' timezone, formatted for humans.
     */
    public function scheduledAtForDisplay(): ?string
    {
        if ($this->published_at === null) {
            return null;
        }

        return $this->published_at->copy()->timezone(config('app.schedule_timezone'))->format('d M Y, h:i A').' IST';
    }

    /**
     * Convert the submitted datetime-local value (editors' timezone) to a UTC moment.
     * Returns null when the content is a draft, nothing was entered, or the time has already passed.
     */
    public static function resolveScheduledAt(?string $input, string $status): ?Carbon
    {
        if ($status !== 'published' || blank($input)) {
            return null;
        }

        $scheduledAt = Carbon::parse($input, config('app.schedule_timezone'))->utc();

        return $scheduledAt->isFuture() ? $scheduledAt : null;
    }

    /**
     * Apply the schedule. The listing date follows the scheduled time so that the
     * post sorts and displays as published at that moment, not when it was drafted.
     */
    public function applySchedule(?Carbon $scheduledAt): void
    {
        $this->published_at = $scheduledAt;

        if ($scheduledAt !== null) {
            $this->forceFill(['created_at' => $scheduledAt]);
        } elseif ($this->created_at !== null && $this->created_at->isFuture()) {
            $this->forceFill(['created_at' => now()]);
        }
    }
}
