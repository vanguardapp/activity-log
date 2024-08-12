<?php

namespace Vanguard\UserActivity\Repositories\Activity;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Vanguard\UserActivity\Activity;

interface ActivityRepository
{
    /**
     * Log user activity.
     *
     * @param  $data  array Array with following fields:
     *               description (string) - Description of user activity.
     *               user_id (int) - User unique identifier.
     *               ip_address (string) - Ip address from which user is accessing the website.
     *               user_agent (string) - User's browser info.
     * @return mixed
     */
    public function log(array $data): Activity;

    /**
     * Paginate activities for user.
     */
    public function paginateActivitiesForUser(
        int $userId,
        int $perPage = 20,
        ?string $search = null
    ): LengthAwarePaginator;

    /**
     * Get specified number of latest user activity logs.
     *
     * @return Collection<Activity>
     */
    public function getLatestActivitiesForUser(int $userId, int $activitiesCount = 10): Collection;

    /**
     * Paginate all activity records.
     */
    public function paginateActivities(int $perPage = 20, ?string $search = null, ?string $name = null): LengthAwarePaginator;

    /**
     * Get count of user activities per day for given period of time.
     */
    public function userActivityForPeriod(int $userId, Carbon $from, Carbon $to): BaseCollection;
}
