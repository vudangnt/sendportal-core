<?php

namespace Sendportal\Base\Services;

use Carbon\Carbon;

class Helper
{

    /**
     * Display a given date in the active user's timezone.
     *
     * @param mixed $date
     * @param string|null $timezone
     * @return Carbon|null
     */
    public function displayDate($date, ?string $timezone = null)
    {
        if (!$date) {
            return null;
        }

        // Use timezone from config if not provided
        if ($timezone === null) {
            $timezone = config('app.timezone', 'UTC');
        }

        return Carbon::parse($date)->copy()->setTimezone($timezone);
    }

    public function isPro(): bool
    {
        return class_exists(\Sendportal\Pro\SendportalProServiceProvider::class);
    }
}
