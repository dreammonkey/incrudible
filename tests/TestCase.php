<?php

namespace Incrudible\Incrudible\Tests;

use Incrudible\Incrudible\IncrudibleServiceProvider;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();

        $this->withoutVite();
    }

    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            IncrudibleServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

        config()->set('incrudible.admins', require __DIR__ . '/../config/incrudible/admins.php');
        config()->set('incrudible.roles', require __DIR__ . '/../config/incrudible/roles.php');
        config()->set('incrudible.permissions', require __DIR__ . '/../config/incrudible/permissions.php');

        $migration = include __DIR__ . '/../database/migrations/create_admins_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
        $migration->up();
    }
}
