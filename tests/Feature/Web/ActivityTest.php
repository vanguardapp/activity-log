<?php

use Carbon\Carbon;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Vanguard\UserActivity\Logger;

uses(TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->logger = app(Logger::class);
    $this->artisan('db:seed');
});

test('display all activities', function () {
    $this->withoutMiddleware();

    $user1 = UserFactory::create();
    $user2 = UserFactory::create();

    Carbon::setTestNow(Carbon::now());

    $this->be($user1);
    $this->logger->log('foo');

    $this->be($user2);
    $this->logger->log('bar');

    $descriptions = descriptions($this->get('activity'));

    $this->assertContains('foo', $descriptions);
    $this->assertContains('bar', $descriptions);
});

test('display activities for a specific user', function () {
    $user = UserFactory::admin()->create();
    $this->be($user);

    $this->logger->log('foo');

    $response = $this->get("activity/user/{$user->id}/log");

    $this->assertSame('user-activity::Index', $response->viewData('page')['component']);
    $this->assertContains('foo', descriptions($response));
});

test('search activities', function () {
    $this->withoutMiddleware();

    $user = UserFactory::create();
    $this->be($user);

    $this->logger->log('foo');
    $this->logger->log('barrr');

    $descriptions = descriptions($this->get('activity?search=foo'));

    $this->assertContains('foo', $descriptions);
    $this->assertNotContains('barrr', $descriptions);
});

/**
 * The activity descriptions listed on the rendered page.
 *
 * @return array<int, string>
 */
function descriptions(TestResponse $response): array
{
    return collect($response->viewData('page')['props']['activities']['data'])
        ->pluck('description')
        ->all();
}
