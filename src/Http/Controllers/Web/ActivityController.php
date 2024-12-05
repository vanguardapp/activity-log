<?php

namespace Vanguard\UserActivity\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityRepository $activities, private readonly UserRepository $userRepository)
    {
    }

    /**
     * Displays the page with activities for all system users.
     */
    public function index(Request $request): View
    {
        $activities = $this->activities->paginateActivities(perPage: 20, search: $request->search, userId: $request->userId);

        return view('user-activity::index', [
            'adminView' => true,
            'activities' => $activities,
            'selectedUser' => $request->userId ? $this->userRepository->find($request->userId) : null
        ]);
    }
}
