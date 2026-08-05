<?php

/**
 * Boot the suite against a Vanguard application.
 *
 * This plugin is written for Vanguard rather than for Laravel in general: its
 * listeners subscribe to App\Events\*, its controllers extend the host's base
 * controller, and its sidebar item is an App\Support\Sidebar\Item. A skeleton
 * application therefore cannot run these tests, so the host is loaded instead.
 *
 * Point VANGUARD_PATH at a checkout, or leave it unset and keep one beside this
 * repository. The plugin must be installed into that checkout, since the tests
 * exercise routes its service provider registers.
 */
$host = getenv('VANGUARD_PATH') ?: dirname(__DIR__, 2).'/vanguard';

$autoload = $host.'/vendor/autoload.php';

if (! is_file($autoload)) {
    fwrite(STDERR, <<<MESSAGE

    Could not find a Vanguard application at: {$host}

    These tests run against the host application. Clone Vanguard beside this
    repository, or set VANGUARD_PATH to an existing checkout:

        VANGUARD_PATH=/path/to/vanguard vendor/bin/phpunit


    MESSAGE);

    exit(1);
}

/** @var Composer\Autoload\ClassLoader $loader */
$loader = require $autoload;

// The host maps this plugin's src through its vendor directory, but never its
// tests, which live under the plugin's own autoload-dev.
$loader->addPsr4('Vanguard\\UserActivity\\Tests\\', __DIR__);
