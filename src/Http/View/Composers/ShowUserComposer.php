<?php

namespace Vanguard\UserActivity\Http\View\Composers;

use Illuminate\View\View;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class ShowUserComposer
{
    public function __construct(private readonly ActivityRepository $activity)
    {
    }

    public function compose(View $view): void
    {
        $user = $view->getData()['user'];

        $view->with('activities', $this->activity->getLatestActivitiesForUser($user->id));
    }
}
