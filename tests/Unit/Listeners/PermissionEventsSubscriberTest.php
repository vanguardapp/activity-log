<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use Vanguard\Events\Permission\Created;
use Vanguard\Events\Permission\Deleted;
use Vanguard\Events\Permission\Updated;

// Manually require the base test case to avoid issues while running automated tests
require_once __DIR__.'/ListenerTestCase.php';

class PermissionEventsSubscriberTest extends \Vanguard\UserActivity\Tests\Unit\Listeners\ListenerTestCase
{
    protected \Vanguard\Permission $perm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->perm = \Vanguard\Permission::factory()->create();
    }

    protected function assertMessageLogged($msg, $user = null, $additional_data = null): void
    {
        $this->assertDatabaseHas('user_activity', [
            'user_id' => $user ? $user->id : $this->user->id,
            'ip_address' => \Request::ip(),
            'user_agent' => \Request::header('User-agent'),
            'description' => $msg,
            'additional_data' => $additional_data ? json_encode($additional_data) : null
        ]);
    }

    /** @test */
    public function onCreate()
    {
        event(new Created($this->perm));
        $this->assertMessageLogged('new_permission', additional_data: ['name' => $this->perm->display_name]);
    }

    /** @test */
    public function onUpdate()
    {
        event(new Updated($this->perm));
        $this->assertMessageLogged('updated_permission', additional_data: ['name' => $this->perm->display_name]);
    }

    /** @test */
    public function onDelete()
    {
        event(new Deleted($this->perm));
        $this->assertMessageLogged('deleted_permission', additional_data: ['name' => $this->perm->display_name]);
    }
}
