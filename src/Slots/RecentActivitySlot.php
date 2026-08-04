<?php

namespace Vanguard\UserActivity\Slots;

use App\Models\User;
use Vanguard\Plugins\Contracts\Slot;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

/**
 * Renders the "Latest Activity" panel on the host's user detail page.
 *
 * Replaces the Vanguard 8 view composer on "user.view", which injected an
 * $activities variable the host template then had to know about.
 */
class RecentActivitySlot implements Slot
{
    public function __construct(private readonly ActivityRepository $activity) {}

    /**
     * {@inheritdoc}
     */
    public function component(): string
    {
        return 'user-activity::RecentActivity';
    }

    /**
     * {@inheritdoc}
     */
    public function data(array $context = []): array
    {
        $user = $context['user'] ?? null;

        if (! $user instanceof User) {
            return ['activities' => [], 'viewAllUrl' => null];
        }

        $activities = $this->activity->getLatestActivitiesForUser($user->id);

        return [
            'activities' => collect($activities)->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'createdAt' => $activity->created_at->format(config('app.date_time_format')),
            ])->all(),
            'viewAllUrl' => route('activity.user', $user->id),
        ];
    }
}
