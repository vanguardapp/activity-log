<?php

namespace Vanguard\UserActivity\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Vanguard\UserActivity\Activity;

class ActivityPayload
{
    /**
     * Serialise a page of activities for the Inertia table.
     *
     * `$linkToUser` is false when the list is already scoped to one user, which
     * is what the Blade template used the presence of $user to decide.
     */
    public static function paginator(LengthAwarePaginator $activities, bool $linkToUser): LengthAwarePaginator
    {
        return $activities->through(fn (Activity $activity) => [
            'id' => $activity->id,
            'userId' => $activity->user_id,
            'userName' => $activity->user?->present()->nameOrEmail ?? '',
            'userUrl' => $linkToUser ? route('activity.user', $activity->user_id) : null,
            'ipAddress' => $activity->ip_address,
            'userAgent' => $activity->user_agent,
            'description' => $activity->description,
            'createdAt' => $activity->created_at->format(config('app.date_time_format')),
        ]);
    }
}
