<?php

use App\Events\Settings\Updated;
use App\Events\User\Banned;
use App\Events\User\ChangedAvatar;
use App\Events\User\Created;
use App\Events\User\Deleted;
use App\Events\User\LoggedIn;
use App\Events\User\LoggedOut;
use App\Events\User\RequestedPasswordResetEmail;
use App\Events\User\TwoFactorDisabled;
use App\Events\User\TwoFactorDisabledByAdmin;
use App\Events\User\TwoFactorEnabled;
use App\Events\User\TwoFactorEnabledByAdmin;
use App\Events\User\UpdatedByAdmin;
use App\Events\User\UpdatedProfileDetails;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use Tests\UpdatesSettings;
use Vanguard\UserActivity\Tests\Unit\Listeners\ListenerTestCase;

uses(ListenerTestCase::class);
uses(UpdatesSettings::class);

beforeEach(function () {
    $this->theUser = User::factory()->create();
});

test('on login', function () {
    event(new LoggedIn);
    $this->assertMessageLogged('Logged in.');
});

test('on logout', function () {
    event(new LoggedOut);
    $this->assertMessageLogged('Logged out.');
});

test('on register', function () {
    $this->setSettings([
        'reg_enabled' => true,
        'reg_email_confirmation' => true,
    ]);

    $user = User::factory()->create();

    event(new Registered($user));

    $this->assertMessageLogged('Created an account.', $user);
});

test('on avatar change', function () {
    event(new ChangedAvatar);
    $this->assertMessageLogged('Updated profile avatar.');
});

test('on profile details update', function () {
    event(new UpdatedProfileDetails);
    $this->assertMessageLogged('Updated profile details.');
});

test('on delete', function () {
    event(new Deleted($this->theUser));

    $message = sprintf(
        'Deleted user %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on ban', function () {
    event(new Banned($this->theUser));

    $message = sprintf(
        'Banned user %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on update by admin', function () {
    event(new UpdatedByAdmin($this->theUser));

    $message = sprintf(
        'Updated profile details for %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on create', function () {
    event(new Created($this->theUser));

    $message = sprintf(
        'Created an account for user %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on settings update', function () {
    event(new Updated);
    $this->assertMessageLogged('Updated website settings.');
});

test('on two factor enable', function () {
    event(new TwoFactorEnabled);
    $this->assertMessageLogged('Enabled Two-Factor Authentication.');
});

test('on two factor disable', function () {
    event(new TwoFactorDisabled);
    $this->assertMessageLogged('Disabled Two-Factor Authentication.');
});

test('on two factor enabled by admin', function () {
    event(new TwoFactorEnabledByAdmin($this->theUser));

    $message = sprintf(
        'Enabled Two-Factor Authentication for user %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on two factor disabled by admin', function () {
    event(new TwoFactorDisabledByAdmin($this->theUser));

    $message = sprintf(
        'Disabled Two-Factor Authentication for user %s.',
        $this->theUser->present()->nameOrEmail
    );

    $this->assertMessageLogged($message);
});

test('on password reset email request', function () {
    event(new RequestedPasswordResetEmail($this->user));
    $this->assertMessageLogged('Requested password reset email.');
});

test('on password reset', function () {
    event(new PasswordReset($this->user));
    $this->assertMessageLogged('Reseted password using "Forgot Password" option.');
});

test('on start impersonating', function () {
    $impersonated = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    event(new TakeImpersonation($this->user, $impersonated));

    $this->assertMessageLogged("Started impersonating user John Doe (ID: {$impersonated->id})");
});

test('on stop impersonating', function () {
    $impersonated = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    event(new LeaveImpersonation($this->user, $impersonated));

    $this->assertMessageLogged("Stopped impersonating user John Doe (ID: {$impersonated->id})");
});
