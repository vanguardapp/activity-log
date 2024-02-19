<?php

namespace Vanguard\UserActivity\Http\Controllers\Api;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class StatsController extends ApiController
{
    public function __construct(private ActivityRepository $activities)
    {
        $this->middleware('auth');
    }

    public function show(): JsonResponse
    {
        return $this->activities->userActivityForPeriod(
            Auth::user()->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        );
    }
}
