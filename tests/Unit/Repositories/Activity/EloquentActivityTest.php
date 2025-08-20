<?php

namespace Vanguard\UserActivity\Tests\Unit\Repositories\Activity;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Assert;
use Tests\TestCase;
use \App\Models\User;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Repositories\Activity\EloquentActivity;

class EloquentActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentActivity
     */
    protected $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = app(EloquentActivity::class);
    }

    public function test_log()
    {
        $user = User::factory()->create();

        Carbon::setTestNow(Carbon::now());

        $data = [
            'user_id' => $user->id,
            'ip_address' => '123.456.789.012',
            'user_agent' => 'foo',
            'description' => 'descriptionnnn',
        ];

        $this->repo->log($data);

        $this->assertDatabaseHas('user_activity', $data);
    }

    public function test_paginate_activities_for_user()
    {
        $user = User::factory()->create();

        $activities = Activity::factory()->times(10)->create(['user_id' => $user->id]);

        $result = $this->repo->paginateActivitiesForUser($user->id, 6)->toArray();

        $this->assertEquals(6, count($result['data']));
        $this->assertEquals(10, $result['total']);
        $this->assertEquals($activities[0]->toArray(), $result['data'][0]);
        $this->assertEquals($activities[5]->toArray(), $result['data'][5]);
    }

    public function test_latest_activities_for_user()
    {
        $user = User::factory()->create();

        Carbon::setTestNow(Carbon::now()->subDay());
        $activities1 = Activity::factory()->times(5)->create(['user_id' => $user->id]);

        Carbon::setTestNow(null);
        $activities2 = Activity::factory()->times(5)->create(['user_id' => $user->id]);

        $result = $this->repo->getLatestActivitiesForUser($user->id, 6)->toArray();

        $this->assertEquals(6, count($result));
        $this->assertEquals($activities2[0]->toArray(), $result[0]);
        $this->assertEquals($activities1[0]->toArray(), $result[5]);
    }

    public function test_paginate_activities()
    {
        $activities = Activity::factory()->times(10)->create();

        $result = $this->repo->paginateActivities(6)->toArray();

        $this->assertEquals(6, count($result['data']));
        $this->assertEquals(10, $result['total']);

        Assert::assertArraySubset($activities[0]->toArray(), $result['data'][0]);
        Assert::assertArraySubset($activities[5]->toArray(), $result['data'][5]);
    }

    public function test_userActivityForPeriod()
    {
        $user = User::factory()->create();
        $now = Carbon::now();

        Carbon::setTestNow($now->copy()->subDays(15));
        Activity::factory()->times(5)->create(['user_id' => $user->id]);

        Carbon::setTestNow($now->copy()->subDays(11));
        Activity::factory()->times(2)->create(['user_id' => $user->id]);

        Carbon::setTestNow($now->copy()->subDays(5));
        Activity::factory()->times(3)->create(['user_id' => $user->id]);

        Carbon::setTestNow($now->copy()->subDays(2));
        Activity::factory()->times(2)->create(['user_id' => $user->id]);

        Carbon::setTestNow(null);

        $result = $this->repo->userActivityForPeriod(
            $user->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        );

        $this->assertEquals($result->get(Carbon::now()->subDays(14)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(13)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(12)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(11)->toDateString()), 2);
        $this->assertEquals($result->get(Carbon::now()->subDays(10)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(9)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(8)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(7)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(6)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(5)->toDateString()), 3);
        $this->assertEquals($result->get(Carbon::now()->subDays(4)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(3)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->subDays(2)->toDateString()), 2);
        $this->assertEquals($result->get(Carbon::now()->subDays(1)->toDateString()), 0);
        $this->assertEquals($result->get(Carbon::now()->toDateString()), 0);
    }
}
