<?php

namespace Vanguard\UserActivity\Tests\Unit\Listeners;

use Tests\UpdatesSettings;

class UserEventsSubscriberTest extends ListenerTestCase
{
    use UpdatesSettings;

    protected \Vanguard\User $theUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theUser = \Vanguard\User::factory()->create();
    }

    /** @test */
    public function onLogin()
    {
        event(new \Vanguard\Events\User\LoggedIn);
        $this->assertMessageLogged('logged_in');
    }

    /** @test */
    public function onLogout()
    {
        event(new \Vanguard\Events\User\LoggedOut());
        $this->assertMessageLogged('logged_out');
    }

    /** @test */
    public function onRegister()
    {
        $this->setSettings([
            'reg_enabled' => true,
            'reg_email_confirmation' => true,
        ]);

        $user = \Vanguard\User::factory()->create();

        event(new \Illuminate\Auth\Events\Registered($user));

        $this->assertMessageLogged('created_account', $user);
    }

    /** @test */
    public function onAvatarChange()
    {
        event(new \Vanguard\Events\User\ChangedAvatar);
        $this->assertMessageLogged('updated_avatar');
    }

    /** @test */
    public function onProfileDetailsUpdate()
    {
        event(new \Vanguard\Events\User\UpdatedProfileDetails);
        $this->assertMessageLogged('updated_profile');
    }

    /** @test */
    public function onDelete()
    {
        event(new \Vanguard\Events\User\Deleted($this->theUser));

        $this->assertMessageLogged('deleted_user', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onBan()
    {
        event(new \Vanguard\Events\User\Banned($this->theUser));

        $this->assertMessageLogged('banned_user', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onUpdateByAdmin()
    {
        event(new \Vanguard\Events\User\UpdatedByAdmin($this->theUser));

        $this->assertMessageLogged('updated_profile_details_for', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onCreate()
    {
        event(new \Vanguard\Events\User\Created($this->theUser));

        $this->assertMessageLogged('created_account_for', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onSettingsUpdate()
    {
        event(new \Vanguard\Events\Settings\Updated);
        $this->assertMessageLogged('updated_settings');
    }

    /** @test */
    public function onTwoFactorEnable()
    {
        event(new \Vanguard\Events\User\TwoFactorEnabled);
        $this->assertMessageLogged('enabled_2fa');
    }

    /** @test */
    public function onTwoFactorDisable()
    {
        event(new \Vanguard\Events\User\TwoFactorDisabled);
        $this->assertMessageLogged('disabled_2fa');
    }

    /** @test */
    public function onTwoFactorEnabledByAdmin()
    {
        event(new \Vanguard\Events\User\TwoFactorEnabledByAdmin($this->theUser));

        $this->assertMessageLogged('enabled_2fa_for', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onTwoFactorDisabledByAdmin()
    {
        event(new \Vanguard\Events\User\TwoFactorDisabledByAdmin($this->theUser));

        $this->assertMessageLogged('disabled_2fa_for', additional_data: ['name' => $this->theUser->present()->nameOrEmail]);
    }

    /** @test */
    public function onPasswordResetEmailRequest()
    {
        event(new \Vanguard\Events\User\RequestedPasswordResetEmail($this->user));
        $this->assertMessageLogged('requested_password_reset');
    }

    /** @test */
    public function onPasswordReset()
    {
        event(new \Illuminate\Auth\Events\PasswordReset($this->user));
        $this->assertMessageLogged('reseted_password');
    }

    /** @test */
    public function onStartImpersonating()
    {
        $impersonated = \Vanguard\User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        event(new \Lab404\Impersonate\Events\TakeImpersonation($this->user, $impersonated));

        $this->assertMessageLogged('started_impersonating', additional_data: ['id' => $impersonated->id, 'name' => 'John Doe']);
    }

    /** @test */
    public function onStopImpersonating()
    {
        $impersonated = \Vanguard\User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        event(new \Lab404\Impersonate\Events\LeaveImpersonation($this->user, $impersonated));

        $this->assertMessageLogged('stopped_impersonating', additional_data: ['id' => $impersonated->id, 'name' => 'John Doe']);
    }
}
