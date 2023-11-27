<?php

namespace Incrudible\Incrudible\Tests;

use Incrudible\Incrudible\IncrudibleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

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
            IncrudibleServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_admins_table.php.stub';
        $migration->up();
    }
}
