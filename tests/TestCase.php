<?php

namespace Incrudible\Incrudible\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;
use Incrudible\Incrudible\IncrudibleServiceProvider;
use LaracraftTech\LaravelSchemaRules\Resolvers\SchemaRulesResolverSqlite;
use LaracraftTech\LaravelSchemaRules\Contracts\SchemaRulesResolverInterface;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Binding the schema rules interface to the SQLite driver
        $this->app->bind(SchemaRulesResolverInterface::class, SchemaRulesResolverSqlite::class);

        $this->loadLaravelMigrations();

        $this->withoutVite();
    }

    protected function getPackageProviders($app)
    {
        return [
            IncrudibleServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Make sure the database is in memory
        config()->set('database.default', 'testing');

        // Manually set the configuration for builtin cruds
        config()->set('incrudible.admins', require __DIR__ . '/../config/incrudible/admins.php');

        $migration = include __DIR__ . '/../database/migrations/create_admins_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
        $migration->up();
    }
}
