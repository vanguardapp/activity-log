<?php

namespace Vanguard\UserActivity\Tests\Feature\Api;

use Facades\Tests\Setup\UserFactory;
use Tests\Feature\ApiTestCase;
use \App\Models\User;
use Vanguard\UserActivity\Activity;
use Vanguard\UserActivity\Http\Resources\ActivityResource;

class ActivityTest extends ApiTestCase
{
    public function test_unauthenticated()
    {
        $this->getJson('/api/activity')->assertStatus(401);
    }

    public function test_get_activities_without_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user, self::API_GUARD)
            ->getJson('/api/activity')
            ->assertStatus(403);
    }

    public function test_paginate_activities()
    {
        $user = $this->getUser();
        $user2 = User::factory()->create();

        $activities = Activity::factory()->times(25)->create(['user_id' => $user->id]);

        Activity::factory()->times(10)->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user, self::API_GUARD)->getJson('/api/activity');

        $transformed = ActivityResource::collection($activities->take(20))->resolve();

        $this->assertEquals($response->json('data'), $transformed);
        $response->assertJson([
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'to' => 20,
                'last_page' => 2,
                'path' => url('api/activity'),
                'total' => 35,
                'per_page' => 20,
            ],
        ]);
    }

    public function test_paginate_activities_with_search_param()
    {
        $user = $this->getUser();

        $set1 = Activity::factory()->times(10)->create([
            'user_id' => $user->id,
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);

        $set2 = Activity::factory()->times(5)->create([
            'user_id' => $user->id,
            'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...',
        ]);

        $transformed = ActivityResource::collection($set2)->resolve();

        $response = $this->actingAs($user, self::API_GUARD)
            ->getJson('/api/activity?filter[description]=minim&per_page=10&sort=created_at')
            ->assertOk();

        $this->assertEquals($response->json('data'), $transformed);
        $response->assertJson([
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'to' => 5,
                'last_page' => 1,
                'total' => 5,
                'per_page' => 10,
                'path' => url('api/activity'),
            ],
        ]);
    }

    public function test_paginate_activities_with_more_records_per_page_than_allowed()
    {
        $this->actingAs($this->getUser(), self::API_GUARD)
            ->getJson('/api/activity?per_page=140')
            ->assertStatus(422);
    }

    public function test_paginate_activities_for_user()
    {
        $user = UserFactory::user()->withPermissions('users.activity')->create();

        $this->be($user, self::API_GUARD);

        $activities = Activity::factory()->times(25)->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/activity?filters[user]={$user->id}");

        $transformed = ActivityResource::collection($activities->take(20))->resolve();

        $this->assertEquals($response->json('data'), $transformed);
        $response->assertJson([
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'to' => 20,
                'last_page' => 2,
                'path' => url('api/activity'),
                'total' => 25,
                'per_page' => 20,
            ],
        ]);
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        return UserFactory::user()->withPermissions('users.activity')->create();
    }
}
