<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Events\Dispatcher;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use App\Events\Settings\Updated as SettingsUpdated;
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
use Vanguard\UserActivity\Logger;

class UserEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onLogin(LoggedIn $event): void
    {
        $this->logger->log(trans('user-activity::log.logged_in'));
    }

    public function onLogout(LoggedOut $event): void
    {
        $this->logger->log(trans('user-activity::log.logged_out'));
    }

    public function onRegister(Registered $event): void
    {
        $this->logger->setUser($event->user);
        $this->logger->log(trans('user-activity::log.created_account'));
    }

    public function onAvatarChange(ChangedAvatar $event): void
    {
        $this->logger->log(trans('user-activity::log.updated_avatar'));
    }

    public function onProfileDetailsUpdate(UpdatedProfileDetails $event): void
    {
        $this->logger->log(trans('user-activity::log.updated_profile'));
    }

    public function onDelete(Deleted $event): void
    {
        $message = trans(
            'user-activity::log.deleted_user',
            ['name' => $event->getDeletedUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onBan(Banned $event): void
    {
        $message = trans(
            'user-activity::log.banned_user',
            ['name' => $event->getBannedUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onUpdateByAdmin(UpdatedByAdmin $event): void
    {
        $message = trans(
            'user-activity::log.updated_profile_details_for',
            ['name' => $event->getUpdatedUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onCreate(Created $event): void
    {
        $message = trans(
            'user-activity::log.created_account_for',
            ['name' => $event->getCreatedUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onSettingsUpdate(SettingsUpdated $event): void
    {
        $this->logger->log(trans('user-activity::log.updated_settings'));
    }

    public function onTwoFactorEnable(TwoFactorEnabled $event): void
    {
        $this->logger->log(trans('user-activity::log.enabled_2fa'));
    }

    public function onTwoFactorDisable(TwoFactorDisabled $event): void
    {
        $this->logger->log(trans('user-activity::log.disabled_2fa'));
    }

    public function onTwoFactorEnableByAdmin(TwoFactorEnabledByAdmin $event): void
    {
        $message = trans(
            'user-activity::log.enabled_2fa_for',
            ['name' => $event->getUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onTwoFactorDisableByAdmin(TwoFactorDisabledByAdmin $event): void
    {
        $message = trans(
            'user-activity::log.disabled_2fa_for',
            ['name' => $event->getUser()->present()->nameOrEmail]
        );

        $this->logger->log($message);
    }

    public function onPasswordResetEmailRequest(RequestedPasswordResetEmail $event): void
    {
        $this->logger->setUser($event->getUser());
        $this->logger->log(trans('user-activity::log.requested_password_reset'));
    }

    public function onPasswordReset(PasswordReset $event): void
    {
        $this->logger->setUser($event->user);
        $this->logger->log(trans('user-activity::log.reseted_password'));
    }

    public function onStartImpersonating(TakeImpersonation $event): void
    {
        $this->logger->setUser($event->impersonator);

        $message = trans('user-activity::log.started_impersonating', [
            'id' => $event->impersonated->id,
            'name' => $event->impersonated->present()->name,
        ]);

        $this->logger->log($message);
    }

    public function onStopImpersonating(LeaveImpersonation $event): void
    {
        $this->logger->setUser($event->impersonator);

        $message = trans('user-activity::log.stopped_impersonating', [
            'id' => $event->impersonated->id,
            'name' => $event->impersonated->present()->name,
        ]);

        $this->logger->log($message);
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
