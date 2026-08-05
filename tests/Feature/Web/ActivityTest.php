<?php

namespace Vanguard\UserActivity\Tests\Feature\Web;

use Carbon\Carbon;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Vanguard\UserActivity\Logger;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = app(Logger::class);
        $this->artisan('db:seed');
    }

    public function test_display_all_activities()
    {
        $this->withoutMiddleware();

        $user1 = UserFactory::create();
        $user2 = UserFactory::create();

        Carbon::setTestNow(Carbon::now());

        $this->be($user1);
        $this->logger->log('foo');

        $this->be($user2);
        $this->logger->log('bar');

        $descriptions = $this->descriptions($this->get('activity'));

        $this->assertContains('foo', $descriptions);
        $this->assertContains('bar', $descriptions);
    }

    public function test_display_activities_for_a_specific_user()
    {
        $user = UserFactory::admin()->create();
        $this->be($user);

        $this->logger->log('foo');

        $response = $this->get("activity/user/{$user->id}/log");

        $this->assertSame('user-activity::Index', $response->viewData('page')['component']);
        $this->assertContains('foo', $this->descriptions($response));
    }

    public function test_search_activities()
    {
        $this->withoutMiddleware();

        $user = UserFactory::create();
        $this->be($user);

        $this->logger->log('foo');
        $this->logger->log('barrr');

        $descriptions = $this->descriptions($this->get('activity?search=foo'));

        $this->assertContains('foo', $descriptions);
        $this->assertNotContains('barrr', $descriptions);
    }

    /**
     * The activity descriptions listed on the rendered page.
     *
     * @return array<int, string>
     */
    private function descriptions(TestResponse $response): array
    {
        return collect($response->viewData('page')['props']['activities']['data'])
            ->pluck('description')
            ->all();
    }
}
