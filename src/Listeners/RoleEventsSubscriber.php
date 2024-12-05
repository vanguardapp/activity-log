<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Events\Dispatcher;
use Vanguard\Events\Role\Created;
use Vanguard\Events\Role\Deleted;
use Vanguard\Events\Role\PermissionsUpdated;
use Vanguard\Events\Role\Updated;
use Vanguard\UserActivity\Logger;
use Vanguard\UserActivity\Support\Enum\ActivityTypes;

class RoleEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onCreate(Created $event): void
    {
        $this->logger->log(ActivityTypes::NEW_ROLE, ['name' => $event->getRole()->display_name]);
    }

    public function onUpdate(Updated $event): void
    {
        $this->logger->log(ActivityTypes::UPDATED_ROLE, ['name' => $event->getRole()->display_name]);
    }

    public function onDelete(Deleted $event): void
    {
        $this->logger->log(ActivityTypes::DELETED_ROLE, ['name' => $event->getRole()->display_name]);
    }

    public function onPermissionsUpdate(PermissionsUpdated $event): void
    {
        $this->logger->log(ActivityTypes::UPDATED_ROLE_PERMISSIONS);
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
