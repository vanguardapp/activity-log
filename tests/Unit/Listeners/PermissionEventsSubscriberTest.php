<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use App\Events\Permission\Created;
use App\Events\Permission\Deleted;
use App\Events\Permission\Updated;

// Manually require the base test case to avoid issues while running automated tests
require_once __DIR__.'/ListenerTestCase.php';

class PermissionEventsSubscriberTest extends \Vanguard\UserActivity\Tests\Unit\Listeners\ListenerTestCase
{
    protected \App\Models\Permission $perm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->perm = \App\Models\Permission::factory()->create();
    }

    protected function assertMessageLogged($msg, $user = null): void
    {
        $this->assertDatabaseHas('user_activity', [
            'user_id' => $user ? $user->id : $this->user->id,
            'ip_address' => \Request::ip(),
            'user_agent' => \Request::header('User-agent'),
            'description' => $msg,
        ]);
    }

    public function test_onCreate()
    {
        event(new Created($this->perm));
        $this->assertMessageLogged("Created new permission called {$this->perm->display_name}.");
    }

    public function test_onUpdate()
    {
        event(new Updated($this->perm));
        $this->assertMessageLogged("Updated the permission named {$this->perm->display_name}.");
    }

    public function test_onDelete()
    {
        event(new Deleted($this->perm));
        $this->assertMessageLogged("Deleted permission named {$this->perm->display_name}.");
    }
}
