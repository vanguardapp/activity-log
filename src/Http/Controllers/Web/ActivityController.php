<?php

namespace Vanguard\UserActivity\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;
use Vanguard\UserActivity\Support\ActivityPayload;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityRepository $activities) {}

    /**
     * Displays the page with activities for all system users.
     */
    public function index(Request $request): InertiaResponse
    {
        $activities = $this->activities->paginateActivities(perPage: 20, search: $request->search);

        return Inertia::render('user-activity::Index', [
            'activities' => ActivityPayload::paginator($activities, linkToUser: true),
            'adminView' => true,
            'user' => null,
            'filters' => ['search' => (string) $request->search],
        ]);
    }
}
