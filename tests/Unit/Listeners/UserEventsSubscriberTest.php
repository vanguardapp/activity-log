<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use Tests\UpdatesSettings;

class UserEventsSubscriberTest extends ListenerTestCase
{
    use UpdatesSettings;

    protected \App\Models\User $theUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theUser = \App\Models\User::factory()->create();
    }

    public function test_onLogin()
    {
        event(new \App\Events\User\LoggedIn);
        $this->assertMessageLogged('Logged in.');
    }

    public function test_onLogout()
    {
        event(new \App\Events\User\LoggedOut());
        $this->assertMessageLogged('Logged out.');
    }

    public function test_onRegister()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => true,
        ]);

        $user = \App\Models\User::factory()->create();

        event(new \Illuminate\Auth\Events\Registered($user));

        $this->assertMessageLogged('Created an account.', $user);
    }

    public function test_onAvatarChange()
    {
        event(new \App\Events\User\ChangedAvatar);
        $this->assertMessageLogged('Updated profile avatar.');
    }

    public function test_onProfileDetailsUpdate()
    {
        event(new \App\Events\User\UpdatedProfileDetails);
        $this->assertMessageLogged('Updated profile details.');
    }

    public function test_onDelete()
    {
        event(new \App\Events\User\Deleted($this->theUser));

        $message = sprintf(
            'Deleted user %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onBan()
    {
        event(new \App\Events\User\Banned($this->theUser));

        $message = sprintf(
            'Banned user %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onUpdateByAdmin()
    {
        event(new \App\Events\User\UpdatedByAdmin($this->theUser));

        $message = sprintf(
            'Updated profile details for %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onCreate()
    {
        event(new \App\Events\User\Created($this->theUser));

        $message = sprintf(
            'Created an account for user %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onSettingsUpdate()
    {
        event(new \App\Events\Settings\Updated);
        $this->assertMessageLogged('Updated website settings.');
    }

    public function test_onTwoFactorEnable()
    {
        event(new \App\Events\User\TwoFactorEnabled);
        $this->assertMessageLogged('Enabled Two-Factor Authentication.');
    }

    public function test_onTwoFactorDisable()
    {
        event(new \App\Events\User\TwoFactorDisabled);
        $this->assertMessageLogged('Disabled Two-Factor Authentication.');
    }

    public function test_onTwoFactorEnabledByAdmin()
    {
        event(new \App\Events\User\TwoFactorEnabledByAdmin($this->theUser));

        $message = sprintf(
            'Enabled Two-Factor Authentication for user %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onTwoFactorDisabledByAdmin()
    {
        event(new \App\Events\User\TwoFactorDisabledByAdmin($this->theUser));

        $message = sprintf(
            'Disabled Two-Factor Authentication for user %s.',
            $this->theUser->present()->nameOrEmail
        );

        $this->assertMessageLogged($message);
    }

    public function test_onPasswordResetEmailRequest()
    {
        event(new \App\Events\User\RequestedPasswordResetEmail($this->user));
        $this->assertMessageLogged('Requested password reset email.');
    }

    public function test_onPasswordReset()
    {
        event(new \Illuminate\Auth\Events\PasswordReset($this->user));
        $this->assertMessageLogged('Reseted password using "Forgot Password" option.');
    }

    public function test_onStartImpersonating()
    {
        $impersonated = \App\Models\User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        event(new \Lab404\Impersonate\Events\TakeImpersonation($this->user, $impersonated));

        $this->assertMessageLogged("Started impersonating user John Doe (ID: {$impersonated->id})");
    }

    public function test_onStopImpersonating()
    {
        $impersonated = \App\Models\User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        event(new \Lab404\Impersonate\Events\LeaveImpersonation($this->user, $impersonated));

        $this->assertMessageLogged("Stopped impersonating user John Doe (ID: {$impersonated->id})");
    }
}
