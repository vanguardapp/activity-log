<?php

namespace Vanguard\UserActivity;

use App\Support\Sidebar\Item;
use Event;
use Illuminate\Contracts\Container\BindingResolutionException;
use Route;
use Vanguard\Plugins\Plugin;
use Vanguard\Plugins\Vanguard;
use Vanguard\UserActivity\Listeners\PermissionEventsSubscriber;
use Vanguard\UserActivity\Listeners\RoleEventsSubscriber;
use Vanguard\UserActivity\Listeners\UserEventsSubscriber;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;
use Vanguard\UserActivity\Repositories\Activity\EloquentActivity;
use Vanguard\UserActivity\Slots\RecentActivitySlot;

class UserActivity extends Plugin
{
    /**
     * {@inheritDoc}
     */
    public function sidebar(): Item
    {
        return Item::create(__('Activity Log'))
            ->route('activity.index')
            ->icon('fas fa-server')
            ->active('activity*')
            ->permissions('users.activity');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ActivityRepository::class, EloquentActivity::class);
    }

    /**
     * Bootstrap services.
     *
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'user-activity');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishAssets();

        $this->app->booted(function () {
            $this->mapWebRoutes();

            if ($this->app['config']->get('auth.expose_api')) {
                $this->mapApiRoutes();
            }
        });

        $this->registerSlots();

        $this->registerEventListeners();
    }

    /**
     * The compiled plugin bundle published to public/vendor/plugins/user-activity.
     *
     * {@inheritdoc}
     */
    public function assets(): array
    {
        return $this->viteAssets('user-activity');
    }

    /**
     * Publish the compiled plugin bundle.
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/plugins/user-activity'),
        ], 'public');
    }

    /**
     * Map web plugin related routes.
     */
    protected function mapWebRoutes(): void
    {
        Route::group([
            'namespace' => 'Vanguard\UserActivity\Http\Controllers\Web',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Map API plugin related routes.
     */
    protected function mapApiRoutes(): void
    {
        Route::group([
            'namespace' => 'Vanguard\UserActivity\Http\Controllers\Api',
            'middleware' => 'api',
            'prefix' => 'api',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Register event subscribers for the plugin.
     */
    private function registerEventListeners(): void
    {
        Event::subscribe(PermissionEventsSubscriber::class);
        Event::subscribe(RoleEventsSubscriber::class);
        Event::subscribe(UserEventsSubscriber::class);
    }

    /**
     * Register the UI slots this plugin contributes to the host.
     */
    private function registerSlots(): void
    {
        Vanguard::slot('user:show', RecentActivitySlot::class);
    }
}
