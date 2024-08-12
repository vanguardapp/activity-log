<?php

namespace Vanguard\UserActivity\Repositories\Activity;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;
use Vanguard\UserActivity\Activity;

class EloquentActivity implements ActivityRepository
{
    /**
     * {@inheritdoc}
     */
    public function log($data): Activity
    {
        return Activity::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginateActivitiesForUser(
        int $userId,
        int $perPage = 20,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Activity::where('user_id', $userId);

        return $this->paginateAndFilterResults($perPage, $search, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestActivitiesForUser(int $userId, int $activitiesCount = 10): Collection
    {
        return Activity::where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($activitiesCount)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function paginateActivities(int $perPage = 20, ?string $search = null, ?string $name = null): LengthAwarePaginator
    {
        $query = Activity::with('user')
            ->when($name,
                fn($q) => $q->whereRaw('LOWER(JSON_EXTRACT(additional_data, "$.name")) LIKE ?', ['%' . strtolower($name) . '%'])
            );

        return $this->paginateAndFilterResults($perPage, $search, $query);
    }

    private function paginateAndFilterResults($perPage, $search, $query): LengthAwarePaginator
    {
        if ($search) {
            $query->where('description', 'LIKE', "%$search%");
        }

        $result = $query->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function userActivityForPeriod($userId, Carbon $from, Carbon $to): BaseCollection
    {
        $result = Activity::select([
            DB::raw('DATE(created_at) as day'),
            DB::raw('count(id) as count'),
        ])
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->pluck('count', 'day');

        while (! $from->isSameDay($to)) {
            if (! $result->has($from->toDateString())) {
                $result->put($from->toDateString(), 0);
            }
            $from->addDay();
        }

        return $result->sortBy(function ($value, $key) {
            return strtotime($key);
        });
    }
}
