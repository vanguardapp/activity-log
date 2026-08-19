User Activity Log plugin for [Vanguard - Advanced PHP Login and User Management](https://vanguardapp.io)
system.

This plugin was originally part of the Vanguard itself, but it has been extracted as a separate plugin starting from Vanguard 4.

## Installation

This plugin requires Vanguard `12.0.0` or greater.

### Installation via Composer

To install the plugin first you will need to pull it via composer 
by running the following command

```
composer require vanguardapp/activity-log
```

The composer will install the plugin for you as well as its dependencies.

The next step is to register the plugin by adding the 
`\Vanguard\UserActivity\UserActivity::class` 
to the list of Vanguard plugins inside the `VanguardServiceProvider`:

```php
protected function plugins()
    {
        return [
            //...
            \Vanguard\UserActivity\UserActivity::class,
        ];
    }
```

As soon as your plugin is registered, you should publish the 
plugins migrations by running the following command:

```
php artisan vendor:publish --provider="Vanguard\UserActivity\UserActivity"  --tag="migrations"
```

And, as the last step of the installation, you will need to
run the following commands to make all the necessary database modifications:

```
php artisan migrate
php artisan db:seed --class="ActivityPermissionsSeeder"
```

At this point the plugin will be fully installed and ready to go.
It is configured to listen for most of the events that are coming from
Vanguard and to put the into the activity log.

### Manual Installation

If you plan to make the modifications to the plugin and customize it to
fit your needs, it's much easier if you add it to your project manually.

To do so, you will need to download the ZIP archive from GitHub
by clicking the green "Clone or download" button and then choosing
the "Download ZIP" option from the dropdown.

Once you have the ZIP file on your computer, extract it to the 
`plugins/ActivityLog` folder (you will need to create this folder
since it probably won't be present in your Vanguard installation).

Next step is to update your main `composer.json` file located in 
Vanguard's root directory and add the following object to the `repositories`
array:

```
{
    "type": "path",
    "url": "./plugins/ActivityLog"
}
```

This will tell the composer that your plugin is located in `/plugins/ActivityLog`
directory and that it should be installed from there. 

Now, add the following to the composer's `require` section 

```
"vanguardapp/activity-log": "*"
```

And run `composer update`.

Composer will now install the plugin from your local directory instead
of pulling it from GitHub, which means that you will be able to make 
the changes to the plugin itself and customize it to fit your needs.

The rest of the process is the same as when the plugin is installed 
by directly fetching it via composer from the GitHub repository, so you
will need to do all the same steps as above, which in short involves 
updating the `VanguardServiceProvider` and running the commands to 
publish plugin's static assets and to update the database.

## Dashboard Widgets

A plugin provides user activity dashboard widget that is visible for all users with a role `User`.

To activate the widget add the `Vanguard\UserActivity\Widgets\ActivityWidget::class` to the widgets array in `VanguardServiceProvider`:

```php
protected function widgets()
{
    return [
       //...
       \Vanguard\UserActivity\Widgets\ActivityWidget::class,
    ];
}
```

## Tests

The suite runs against a Vanguard application, because this plugin is written
for Vanguard rather than for Laravel in general: its listeners subscribe to
`App\Events\*` and its controllers extend the host's base controller.

`tests/bootstrap.php` boots that application's autoloader. This repository has no
`vendor` directory of its own and nothing to `composer install`: the suite runs
on the host's Pest binary.

### Setting up the host

Keep a Vanguard checkout beside this repository, and install the plugin into it
**as a path repository**, since the host's autoloader is what maps
`Vanguard\UserActivity\` onto `src`. In the host's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../vanguardapp-activity-log",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "vanguardapp/activity-log": "*"
    }
}
```

Required from a tag instead, the suite exercises the release sitting in the
host's `vendor` directory and silently ignores your edits.

### Running the suite

```bash
composer test
```

Arguments go after `--`. PHPUnit reads its configuration from the working
directory, so the `phpunit.xml` here is the one that applies and the usual
selectors behave as they normally do:

```bash
composer test -- --compact
composer test -- tests/Feature/Web/ActivityTest.php
composer test -- --filter=stats
```

If the checkout is not at `../vanguard`, point `VANGUARD_PATH` at it. This is
also the hook a pipeline uses:

```bash
VANGUARD_PATH=/path/to/vanguard composer test
```

That default is expanded by the shell, which Windows `cmd` does not do. Call the
binary directly there:

```
..\vanguard\vendor\bin\pest
```

### Why the host's binary

Pest takes its root from the autoloader beside the binary, which is the host. It
boots the host's `tests/Pest.php` and never one placed here, so every file in
this suite names its base class with `uses()` rather than relying on a shared
bootstrap.


## License

This plugin is an open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT). 
