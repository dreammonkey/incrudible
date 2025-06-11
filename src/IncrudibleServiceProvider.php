<?php

namespace Incrudible\Incrudible;

use Incrudible\Incrudible\Traits\RegistersAuthProvider;
use Incrudible\Incrudible\Traits\RegistersMiddleware;
use Incrudible\Incrudible\Traits\RegistersRouteMacros;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class IncrudibleServiceProvider extends PackageServiceProvider
{
    use RegistersAuthProvider;
    use RegistersMiddleware;
    use RegistersRouteMacros;

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('incrudible')
            // ->hasRoute('incrudible')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_admins_table',
            ])
            ->hasCommands([
                \Incrudible\Incrudible\Commands\ScaffoldIncrudible::class,
                \Incrudible\Incrudible\Commands\CreateAdmin::class,
                \Incrudible\Incrudible\Commands\Crud\CrudMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Config\CrudConfigMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Model\CrudModelMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Controller\CrudControllerMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Request\CrudRequestMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Resource\CrudResourceMakeCommand::class,
                \Incrudible\Incrudible\Commands\Crud\Frontend\CrudFrontendMakeCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {

                $command
                    ->publishConfigFile()
                    ->publishMigrations();
                // ->publishAssets()
                // ->askToRunMigrations()
                // ->askToStarRepoOnGitHub('dreammonkey/incrudible')
            });
    }

    public function bootingPackage()
    {
        // JsonResource::withoutWrapping();

        $this->loadHelpers();
        $this->registerAuthProvider();
        $this->registerMiddlewareGroup(app()->router);
        $this->registerMiddlewareAliases(app()->router);
        $this->registerRouteMacros();
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../routes/incrudible.php' => base_path('routes/incrudible.php'),
        ], 'incrudible-routes');

        $this->publishes([
            __DIR__ . '/../config/incrudible/admins.php' => config_path('incrudible/admins.php'),
            __DIR__ . '/../config/incrudible/roles.php' => config_path('incrudible/roles.php'),
            __DIR__ . '/../config/incrudible/permissions.php' => config_path('incrudible/permissions.php'),
        ], 'incrudible-config');

        $this->loadRoutesFrom(
            file_exists(base_path('routes/incrudible.php'))
                ? base_path('routes/incrudible.php')
                : __DIR__ . '/../routes/incrudible.php'
        );
    }

    /**
     * Load the Incrudible helper methods, for convenience.
     */
    public function loadHelpers()
    {
        require __DIR__ . '/helpers.php';
    }
}
