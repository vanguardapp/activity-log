<?php

namespace Vanguard\UserActivity\Listeners;

use Illuminate\Events\Dispatcher;
use Vanguard\Events\Permission\Created;
use Vanguard\Events\Permission\Deleted;
use Vanguard\Events\Permission\Updated;
use Vanguard\UserActivity\Logger;

class PermissionEventsSubscriber
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function onCreate(Created $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('user-activity::log.new_permission', ['name' => $name]);

        $this->logger->log($message);
    }

    public function onUpdate(Updated $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('user-activity::log.updated_permission', ['name' => $name]);

        $this->logger->log($message);
    }

    public function onDelete(Deleted $event): void
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('user-activity::log.deleted_permission', ['name' => $name]);

        $this->logger->log($message);
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
