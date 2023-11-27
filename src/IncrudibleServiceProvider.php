<?php

namespace Incrudible\Incrudible;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelPackageTools\Package;
use Incrudible\Incrudible\Commands\CreateAdmin;
use Incrudible\Incrudible\Commands\ScaffoldIncrudible;
use Incrudible\Incrudible\Traits\RegistersAuthProvider;
use Incrudible\Incrudible\Traits\RegistersMiddleware;
use Incrudible\Incrudible\Traits\RegistersRouteMacros;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

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
            ])
            ->hasInstallCommand(function (InstallCommand $command) {

                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    // ->publishAssets()
                    // ->askToRunMigrations()
                    // ->askToStarRepoOnGitHub('dreammonkey/incrudible')
                ;
            });
    }

    public function bootingPackage()
    {
        JsonResource::withoutWrapping();

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
