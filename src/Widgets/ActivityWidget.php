<?php

namespace Vanguard\UserActivity\Widgets;

use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use \App\Models\User;
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

    public function render(): View
    {
        return view('user-activity::widgets.user-activity', [
            'activities' => $this->getActivity(),
        ]);
    }

    public function scripts(): View
    {
        return view('user-activity::widgets.user-activity-scripts', [
            'activities' => $this->getActivity(),
        ]);
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
