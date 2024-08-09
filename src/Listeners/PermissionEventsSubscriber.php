<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Events\Dispatcher;
use Vanguard\Events\Permission\Created;
use Vanguard\Events\Permission\Deleted;
use Vanguard\Events\Permission\Updated;
use Vanguard\UserActivity\Logger;
use Vanguard\UserActivity\Support\Enum\ActivityTypes;

class PermissionEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onCreate(Created $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;

        $this->logger->log(ActivityTypes::NEW_PERMISSION, ['name' => $name]);
    }

    public function onUpdate(Updated $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;

        $this->logger->log(ActivityTypes::UPDATED_PERMISSION, ['name' => $name]);
    }

    public function onDelete(Deleted $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;

        $this->logger->log(ActivityTypes::DELETED_PERMISSION, ['name' => $name]);
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
    }
}
