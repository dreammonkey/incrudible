<?php

namespace Incrudible\Incrudible\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Incrudible\Incrudible\IncrudibleServiceProvider;
use LaracraftTech\LaravelSchemaRules\Resolvers\SchemaRulesResolverSqlite;
use LaracraftTech\LaravelSchemaRules\Contracts\SchemaRulesResolverInterface;
use Spatie\Permission\PermissionServiceProvider;

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
        config()->set('database.default', 'testing');

        $migration = include __DIR__ . '/../database/migrations/create_admins_table.php.stub';
        $migration->up();

        $migration = include __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
        $migration->up();
    }
}
