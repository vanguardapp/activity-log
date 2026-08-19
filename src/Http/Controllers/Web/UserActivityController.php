<?php

namespace Vanguard\UserActivity\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;
use Vanguard\UserActivity\Support\ActivityPayload;

class UserActivityController extends Controller
{
    public function __construct(private readonly ActivityRepository $activities) {}

    /**
     * Displays the activity log page for specific user.
     */
    public function index(User $user, Request $request): InertiaResponse
    {
        return $this->render($user, $request, adminView: true);
    }

    /**
     * Display user activity log.
     */
    public function show(Request $request): InertiaResponse
    {
        return $this->render(auth()->user(), $request, adminView: false);
    }

    private function render(User $user, Request $request, bool $adminView): InertiaResponse
    {
        $activities = $this->activities->paginateActivitiesForUser(
            userId: $user->id,
            search: $request->search,
        );

        return Inertia::render('user-activity::Index', [
            // The list is already scoped to one user, so their name is not
            // linked to the very page being viewed.
            'activities' => ActivityPayload::paginator($activities, linkToUser: false),
            'adminView' => $adminView,
            'user' => [
                'id' => $user->id,
                'nameOrEmail' => $user->present()->nameOrEmail,
            ],
            'filters' => ['search' => (string) $request->search],
        ]);
    }
}
