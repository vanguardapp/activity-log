<?php

use App\Events\Role\Created;
use App\Events\Role\Deleted;
use App\Events\Role\PermissionsUpdated;
use App\Events\Role\Updated;
use App\Models\Role;
use Vanguard\UserActivity\Tests\Unit\Listeners\ListenerTestCase;

uses(ListenerTestCase::class);

beforeEach(function () {
    $this->role = Role::factory()->create();
});

test('on create', function () {
    event(new Created($this->role));
    $this->assertMessageLogged("Created new role called {$this->role->display_name}.");
});

test('on update', function () {
    event(new Updated($this->role));
    $this->assertMessageLogged("Updated role with name {$this->role->display_name}.");
});

test('on delete', function () {
    event(new Deleted($this->role));
    $this->assertMessageLogged("Deleted role named {$this->role->display_name}.");
});

test('on permissions update', function () {
    event(new PermissionsUpdated);
    $this->assertMessageLogged('Updated role permissions.');
});
