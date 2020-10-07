<?php

namespace Vanguard\UserActivity\Tests\Feature\Api;

use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Vanguard\User;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class StatsTest extends ApiTestCase
{
    /** @test */
    public function non_admin_users_cannot_get_user_stats()
    {
        $user = User::factory()->create();

        Carbon::setTestNow(Carbon::now()->subWeek());
        Activity::factory()->times(5)->create(['user_id' => $user->id]);

        Carbon::setTestNow(null);
        Activity::factory()->times(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, self::API_GUARD)->getJson("/api/stats/activity");

        $expected = app(ActivityRepository::class)->userActivityForPeriod(
            $user->id,
            Carbon::now()->subWeek(2),
            Carbon::now()
        )->toArray();

        $response->assertOk()
            ->assertJson($expected);
    }
}
