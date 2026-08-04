<?php

namespace Vanguard\UserActivity\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class StatsController extends ApiController
{
    public function __construct(private readonly ActivityRepository $activities)
    {
        $this->middleware('auth');
    }

    public function show(): JsonResponse
    {
        $data = $this->activities->userActivityForPeriod(
            Auth::user()->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        );

        return response()->json($data);
    }
}
