<?php

use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\ApiTestCase;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

uses(ApiTestCase::class);

test('non admin users cannot get user stats', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::now()->subWeek());
    Activity::factory()->times(5)->create(['user_id' => $user->id]);

    Carbon::setTestNow(null);
    Activity::factory()->times(5)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, self::API_GUARD)->getJson('/api/stats/activity');

    $expected = app(ActivityRepository::class)->userActivityForPeriod(
        $user->id,
        Carbon::now()->subWeek(2),
        Carbon::now()
    )->toArray();

    $response->assertOk()
        ->assertJson($expected);
});
