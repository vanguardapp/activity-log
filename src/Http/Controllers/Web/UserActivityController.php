<?php

namespace Vanguard\UserActivity\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Models\User;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class UserActivityController extends Controller
{
    public function __construct(private readonly ActivityRepository $activities)
    {
    }

    /**
     * Displays the activity log page for specific user.
     */
    public function index(User $user, Request $request): View
    {
        $activities = $this->activities->paginateActivitiesForUser(
            userId: $user->id,
            search: $request->search,
        );

        return view('user-activity::index', [
            'user' => $user,
            'adminView' => true,
            'activities' => $activities,
        ]);
    }

    /**
     * Display user activity log.
     */
    public function show(Request $request): View
    {
        $user = auth()->user();

        $activities = $this->activities->paginateActivitiesForUser(
            userId: $user->id,
            search: $request->get('search'),
        );

        return view('user-activity::index', compact('activities', 'user'));
    }
}
