<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Events\Dispatcher;
use Vanguard\Events\Role\Created;
use Vanguard\Events\Role\Deleted;
use Vanguard\Events\Role\PermissionsUpdated;
use Vanguard\Events\Role\Updated;
use Vanguard\UserActivity\Logger;

class RoleEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onCreate(Created $event): void
    {
        $message = trans(
            'user-activity::log.new_role',
            ['name' => $event->getRole()->display_name]
        );

        $this->logger->log($message);
    }

    public function onUpdate(Updated $event): void
    {
        $message = trans(
            'user-activity::log.updated_role',
            ['name' => $event->getRole()->display_name]
        );

        $this->logger->log($message);
    }

    public function onDelete(Deleted $event): void
    {
        $message = trans(
            'user-activity::log.deleted_role',
            ['name' => $event->getRole()->display_name]
        );

        $this->logger->log($message);
    }

    public function onPermissionsUpdate(PermissionsUpdated $event): void
    {
        $this->logger->log(trans('user-activity::log.updated_role_permissions'));
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $class = self::class;

        $events->listen(Created::class, "{$class}@onCreate");
        $events->listen(Updated::class, "{$class}@onUpdate");
        $events->listen(Deleted::class, "{$class}@onDelete");
        $events->listen(PermissionsUpdated::class, "{$class}@onPermissionsUpdate");
    }
}
