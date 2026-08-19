<?php

use App\Events\Permission\Created;
use App\Events\Permission\Deleted;
use App\Events\Permission\Updated;
use App\Models\Permission;
use Vanguard\UserActivity\Tests\Unit\Listeners\ListenerTestCase;

uses(ListenerTestCase::class);

// Manually require the base test case to avoid issues while running automated tests
require_once __DIR__.'/ListenerTestCase.php';

beforeEach(function () {
    $this->perm = Permission::factory()->create();
});

test('on create', function () {
    event(new Created($this->perm));
    $this->assertMessageLogged("Created new permission called {$this->perm->display_name}.");
});

test('on update', function () {
    event(new Updated($this->perm));
    $this->assertMessageLogged("Updated the permission named {$this->perm->display_name}.");
});

test('on delete', function () {
    event(new Deleted($this->perm));
    $this->assertMessageLogged("Deleted permission named {$this->perm->display_name}.");
});
