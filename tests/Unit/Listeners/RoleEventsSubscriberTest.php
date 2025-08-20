<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use App\Events\Role\Created;
use App\Events\Role\Deleted;
use App\Events\Role\PermissionsUpdated;
use App\Events\Role\Updated;

class RoleEventsSubscriberTest extends ListenerTestCase
{
    protected \App\Models\Role $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = \App\Models\Role::factory()->create();
    }

    public function test_onCreate()
    {
        event(new Created($this->role));
        $this->assertMessageLogged("Created new role called {$this->role->display_name}.");
    }

    public function test_onUpdate()
    {
        event(new Updated($this->role));
        $this->assertMessageLogged("Updated role with name {$this->role->display_name}.");
    }

    public function test_onDelete()
    {
        event(new Deleted($this->role));
        $this->assertMessageLogged("Deleted role named {$this->role->display_name}.");
    }

    public function test_onPermissionsUpdate()
    {
        event(new PermissionsUpdated());
        $this->assertMessageLogged('Updated role permissions.');
    }
}
