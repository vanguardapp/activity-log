<?php

namespace Vanguard\UserActivity\Http\Controllers\Api;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Http\Requests\GetActivitiesRequest;
use Vanguard\UserActivity\Http\Resources\ActivityResource;

class ActivityController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.activity');
    }

    /**
     * Paginate user activities.
     */
    public function index(GetActivitiesRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $activities = QueryBuilder::for(Activity::class)
            ->allowedIncludes('user')
            ->allowedFilters([
                AllowedFilter::partial('description'),
                AllowedFilter::exact('user', 'user_id'),
            ])
            ->allowedSorts('created_at')
            ->defaultSort('-created_at')
            ->paginate($request->per_page ?: 20);

        return ActivityResource::collection($activities);
    }
}
