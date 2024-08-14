<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Vanguard\User;

abstract class ListenerTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->be($this->user);
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
}
