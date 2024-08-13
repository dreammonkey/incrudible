<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Filesystem\Filesystem;
use Incrudible\Incrudible\Traits\BreezeHelpers;

class ScaffoldIncrudible extends Command
{
    use BreezeHelpers;

    public $signature = 'incrudible:scaffold
                        {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    public $description = 'Install the Incrudible backend for your Laravel project.';

    public function handle(): int
    {
        // composer require...
        if (version_compare(App::version(), '11.0.0', '>=')) {
            // Laravel >= 11
            if (! $this->requireComposerPackages([
                'inertiajs/inertia-laravel:^1.0',
                'laravel/sanctum:^4.0',
                'tightenco/ziggy:^2.0',
                'laracraft-tech/laravel-schema-rules:^1.4',
                'spatie/laravel-permission:^6.0',
            ])) {
                return 1;
            }
        } else {
            // Laravel < 11
            if (! $this->requireComposerPackages([
                'inertiajs/inertia-laravel:^0.6.8',
                'laravel/sanctum:^3.2',
                'tightenco/ziggy:^2.0',
                'laracraft-tech/laravel-schema-rules:^1.4',
                'spatie/laravel-permission:^6.0',
            ])) {
                return 1;
            }
        }

        // Publish vendor assets...
        $this->call('vendor:publish', [
            '--provider' => "Spatie\Permission\PermissionServiceProvider",
        ]);

        // NPM Packages...
        $this->updatePackageJson(function ($packages) {
            return [
                // TODO: drop headlessui/react, using shadcn instead
                '@headlessui/react' => '^2.0.0',
                '@hookform/resolvers' => '^3.4.2',
                '@inertiajs/react' => '^1.0.0',
                '@radix-ui/react-checkbox' => '^1.0.4',
                '@radix-ui/react-collapsible' => '^1.0.3',
                '@radix-ui/react-dialog' => '^1.0.5',
                '@radix-ui/react-dropdown-menu' => '^2.0.6',
                '@radix-ui/react-label' => '^2.0.2',
                '@radix-ui/react-popover' => '^1.1.1',
                '@radix-ui/react-select' => '^2.0.0',
                '@radix-ui/react-slot' => '^1.0.2',
                '@radix-ui/react-switch' => '^1.1.0',
                '@tailwindcss/forms' => '^0.5.3',
                '@tanstack/react-table' => '^8.17.3',
                '@tanstack/react-query' => '^5.40.0',
                '@types/node' => '^18.13.0',
                '@types/react' => '^18.0.28',
                '@types/react-dom' => '^18.0.10',
                '@vitejs/plugin-react' => '^4.0.3',
                'autoprefixer' => '^10.4.12',
                'axios' => '^1.6.4',
                'class-variance-authority' => '^0.7.0',
                'clsx' => '^2.1.0',
                'cmdk' => '1.0.0',
                'date-fns' => '^3.6.0',
                'laravel-vite-plugin' => '^1.0',
                'lucide-react' => '^0.368.0',
                'postcss' => '^8.4.31',
                'prettier' => '^3.3.2',
                'prettier-plugin-tailwindcss' => '^0.6.3',
                'react' => '^18.2.0',
                'react-day-picker' => '^9.0.4',
                'react-dom' => '^18.2.0',
                'react-hook-form' => '^7.51.5',
                'react-input-mask' => '^2.0.4',
                'tailwind-merge' => '^2.2.2',
                'tailwindcss' => '^3.2.1',
                'tailwindcss-animate' => '^1.0.7',
                'typescript' => '^5.0.2',
                'vite' => '^5.0',
                'web-api-hooks' => '^3.0.2',
                'ziggy-js' => '^1.8.2',
                'zod' => '^3.23.8',
            ] + $packages;
        }, 'devDependencies');

        // Prettier
        $this->updatePackageJson(function ($entries) {
            return [
                'semi' => false,
                'tabWidth' => 2,
                'singleQuote' => true,
                'trailingComma' => 'all',
                'printWidth' => 120,
            ] + $entries;
        }, 'prettier');

        // Copy Incrudible App...
        (new Filesystem)->ensureDirectoryExists(app_path('Incrudible'));
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../../stubs/app/Incrudible',
            app_path('Incrudible')
        );

        // // Views...
        // copy(
        //     __DIR__ . '/../../stubs/resources/views/incrudible.blade.php',
        //     resource_path('views/incrudible.blade.php')
        // );

        // Resources...
        // Components + Pages...
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Incrudible'));
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../../stubs/resources/js/Incrudible',
            resource_path('js/Incrudible')
        );
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../../stubs/resources/js/types',
            resource_path('js/types')
        );
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../../stubs/resources/js/lib',
            resource_path('js/lib')
        );
        copy(
            __DIR__ . '/../../stubs/resources/js/incrudible.tsx',
            resource_path('js/incrudible.tsx')
        );
        copy(
            __DIR__ . '/../../stubs/resources/js/bootstrap.ts',
            resource_path('js/bootstrap.ts')
        );

        // Tailwind / Vite / Typescript / shadcn...
        copy(__DIR__ . '/../../stubs/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../stubs/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__ . '/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__ . '/../../stubs/tsconfig.json', base_path('tsconfig.json'));
        copy(__DIR__ . '/../../stubs/components.json', base_path('components.json'));

        $this->replaceInFile('"vite build"', '"vite build && vite build --ssr"', base_path('package.json'));

        $this->comment('All done');

        return self::SUCCESS;
    }
}
