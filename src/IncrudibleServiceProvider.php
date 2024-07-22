<?php

namespace Incrudible\Incrudible;

use Spatie\LaravelPackageTools\Package;
use Incrudible\Incrudible\Commands\CreateAdmin;
use Illuminate\Http\Resources\Json\JsonResource;
use Incrudible\Incrudible\Traits\RegistersMiddleware;
use Incrudible\Incrudible\Commands\ScaffoldIncrudible;
use Incrudible\Incrudible\Traits\RegistersRouteMacros;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Incrudible\Incrudible\Traits\RegistersAuthProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Incrudible\Incrudible\Commands\GenerateCrudRequests;
use Incrudible\Incrudible\Commands\CrudIndexRequestMakeCommand;
use Incrudible\Incrudible\Commands\CrudStoreRequestMakeCommand;
use Incrudible\Incrudible\Commands\CrudUpdateRequestMakeCommand;

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
            ->hasRoute('incrudible')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_admins_table',
            ])
            ->hasCommands([
                ScaffoldIncrudible::class,
                CreateAdmin::class,
                GenerateCrudRequests::class,
                CrudIndexRequestMakeCommand::class,
                CrudStoreRequestMakeCommand::class,
                CrudUpdateRequestMakeCommand::class,
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

    /**
     * Load the Summus helper methods, for convenience.
     */
    public function loadHelpers()
    {
        require __DIR__ . '/helpers.php';
    }
}
