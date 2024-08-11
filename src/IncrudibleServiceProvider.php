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
    use RegistersAuthProvider, RegistersMiddleware, RegistersRouteMacros;

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
                \Incrudible\Incrudible\Commands\CrudMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudModelMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudFrontEndMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudResourceControllerMakeCommand::class,
                \Incrudible\Incrudible\Commands\GenerateCrudRequests::class,
                \Incrudible\Incrudible\Commands\CrudIndexRequestMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudStoreRequestMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudUpdateRequestMakeCommand::class,
                \Incrudible\Incrudible\Commands\CrudDeleteRequestMakeCommand::class,
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
        $this->registerMiddlewareGroup($this->app->router);
        $this->registerMiddlewareAliases($this->app->router);
        $this->registerRouteMacros();
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../routes/incrudible.php' => base_path('routes/incrudible.php'),
        ], 'incrudible-routes');

        $this->loadRoutesFrom(
            file_exists(base_path('routes/incrudible.php'))
                ? base_path('routes/incrudible.php')
                : __DIR__.'/../routes/incrudible.php'
        );
    }

    /**
     * Load the Summus helper methods, for convenience.
     */
    public function loadHelpers()
    {
        require __DIR__.'/helpers.php';
    }
}
