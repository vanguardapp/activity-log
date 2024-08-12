<?php

namespace Vanguard\UserActivity\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityRepository $activities)
    {
    }

    /**
     * Displays the page with activities for all system users.
     */
    public function index(Request $request): View
    {
        $activities = $this->activities->paginateActivities(perPage: 20, search: $request->search, user_id: $request->user_id);

        return view('user-activity::index', [
            'adminView' => true,
            'activities' => $activities
        ]);
    }
}
