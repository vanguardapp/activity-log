<?php

namespace Vanguard\UserActivity\Widgets;

use App\Models\User;
use Auth;
use Carbon\Carbon;
use Vanguard\Plugins\Widget;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class ActivityWidget extends Widget
{
    /**
     * {@inheritdoc}
     */
    public ?string $width = '12';

    private ?array $userActivity = null;

    public function __construct(private readonly ActivityRepository $activities)
    {
        $this->permissions(function (User $user) {
            return $user->hasRole('User');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function component(): string
    {
        return 'user-activity::ActivityWidget';
    }

    /**
     * {@inheritdoc}
     */
    public function data(): array
    {
        $activity = $this->getActivity();

        return [
            'title' => __('User Activity'),
            'labels' => array_keys($activity),
            'values' => array_values($activity),
            // Kept as separate keys so the existing de/sr translations apply.
            'units' => [
                'action' => __('action'),
                'actions' => __('actions'),
            ],
        ];
    }

    private function getActivity(): array
    {
        if ($this->userActivity) {
            return $this->userActivity;
        }

        return $this->userActivity = $this->activities->userActivityForPeriod(
            Auth::user()->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        )->toArray();
    }
}
