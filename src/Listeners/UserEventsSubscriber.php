<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use Vanguard\Events\Settings\Updated as SettingsUpdated;
use Vanguard\Events\User\Banned;
use Vanguard\Events\User\ChangedAvatar;
use Vanguard\Events\User\Created;
use Vanguard\Events\User\Deleted;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Events\User\RequestedPasswordResetEmail;
use Vanguard\Events\User\TwoFactorDisabled;
use Vanguard\Events\User\TwoFactorDisabledByAdmin;
use Vanguard\Events\User\TwoFactorEnabled;
use Vanguard\Events\User\TwoFactorEnabledByAdmin;
use Vanguard\Events\User\UpdatedByAdmin;
use Vanguard\Events\User\UpdatedProfileDetails;
use Vanguard\UserActivity\Logger;
use Vanguard\UserActivity\Support\Enum\ActivityTypes;

class UserEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onLogin(LoggedIn $event): void
    {
        $this->logger->log(ActivityTypes::LOGGED_IN);
    }

    public function onLogout(LoggedOut $event): void
    {
        $this->logger->log(ActivityTypes::LOGGED_OUT);
    }

    public function onRegister(Registered $event): void
    {
        $this->logger->setUser($event->user);
        $this->logger->log(ActivityTypes::CREATED_ACCOUNT);
    }

    public function onAvatarChange(ChangedAvatar $event): void
    {
        $this->logger->log(ActivityTypes::UPDATED_AVATAR);
    }

    public function onProfileDetailsUpdate(UpdatedProfileDetails $event): void
    {
        $this->logger->log(ActivityTypes::UPDATED_PROFILE);
    }

    public function onDelete(Deleted $event): void
    {
        $this->logger->log(
            ActivityTypes::DELETED_USER,
            [
                'name' => $event->getDeletedUser()->present()->nameOrEmail
            ]
        );
    }

    public function onBan(Banned $event): void
    {
        $this->logger->log(
            ActivityTypes::BANNED_USER,
            [
                'name' => $event->getBannedUser()->present()->nameOrEmail
            ]
        );
    }

    public function onUpdateByAdmin(UpdatedByAdmin $event): void
    {
        $this->logger->log(
            ActivityTypes::UPDATED_PROFILE_DETAILS_FOR,
            [
                'name' => $event->getUpdatedUser()->present()->nameOrEmail
            ]
        );
    }

    public function onCreate(Created $event): void
    {
        $this->logger->log(
            ActivityTypes::CREATED_ACCOUNT_FOR,
            [
                'name' => $event->getCreatedUser()->present()->nameOrEmail
            ]
        );
    }

    public function onSettingsUpdate(SettingsUpdated $event): void
    {
        $this->logger->log(ActivityTypes::UPDATED_SETTINGS);
    }

    public function onTwoFactorEnable(TwoFactorEnabled $event): void
    {
        $this->logger->log(ActivityTypes::ENABLED_2FA);
    }

    public function onTwoFactorDisable(TwoFactorDisabled $event): void
    {
        $this->logger->log(ActivityTypes::DISABLED_2FA);
    }

    public function onTwoFactorEnableByAdmin(TwoFactorEnabledByAdmin $event): void
    {
        $this->logger->log(
            ActivityTypes::ENABLED_2FA_FOR,
            [
                'name' => $event->getUser()->present()->nameOrEmail
            ]
        );
    }

    public function onTwoFactorDisableByAdmin(TwoFactorDisabledByAdmin $event): void
    {
        $this->logger->log(
            ActivityTypes::DISABLED_2FA_FOR,
            [
                'name' => $event->getUser()->present()->nameOrEmail
            ]
        );
    }

    public function onPasswordResetEmailRequest(RequestedPasswordResetEmail $event): void
    {
        $this->logger->setUser($event->getUser());
        $this->logger->log(ActivityTypes::REQUESTED_PASSWORD_RESET);
    }

    public function onPasswordReset(PasswordReset $event): void
    {
        $this->logger->setUser($event->user);
        $this->logger->log(ActivityTypes::RESETED_PASSWORD);
    }

    public function onStartImpersonating(TakeImpersonation $event): void
    {
        $this->logger->setUser($event->impersonator);

        $this->logger->log(ActivityTypes::STARTED_IMPERSONATING, [
            'id' => $event->impersonated->id,
            'name' => $event->impersonated->present()->name,
        ]);
    }

    public function onStopImpersonating(LeaveImpersonation $event): void
    {
        $this->logger->setUser($event->impersonator);

        $this->logger->log(ActivityTypes::STOPPED_IMPERSONATING, [
            'id' => $event->impersonated->id,
            'name' => $event->impersonated->present()->name,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events)
    {
        $class = self::class;

        $events->listen(LoggedIn::class, "{$class}@onLogin");
        $events->listen(LoggedOut::class, "{$class}@onLogout");
        $events->listen(Registered::class, "{$class}@onRegister");
        $events->listen(Created::class, "{$class}@onCreate");
        $events->listen(ChangedAvatar::class, "{$class}@onAvatarChange");
        $events->listen(UpdatedProfileDetails::class, "{$class}@onProfileDetailsUpdate");
        $events->listen(UpdatedByAdmin::class, "{$class}@onUpdateByAdmin");
        $events->listen(Deleted::class, "{$class}@onDelete");
        $events->listen(Banned::class, "{$class}@onBan");
        $events->listen(SettingsUpdated::class, "{$class}@onSettingsUpdate");
        $events->listen(TwoFactorEnabled::class, "{$class}@onTwoFactorEnable");
        $events->listen(TwoFactorDisabled::class, "{$class}@onTwoFactorDisable");
        $events->listen(TwoFactorEnabledByAdmin::class, "{$class}@onTwoFactorEnableByAdmin");
        $events->listen(TwoFactorDisabledByAdmin::class, "{$class}@onTwoFactorDisableByAdmin");
        $events->listen(RequestedPasswordResetEmail::class, "{$class}@onPasswordResetEmailRequest");
        $events->listen(PasswordReset::class, "{$class}@onPasswordReset");
        $events->listen(TakeImpersonation::class, "{$class}@onStartImpersonating");
        $events->listen(LeaveImpersonation::class, "{$class}@onStopImpersonating");
    }
}
