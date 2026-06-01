<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Console\Command;
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
        if (! $this->requireComposerPackages([
            'inertiajs/inertia-laravel:^3.0',
            'laravel/sanctum:^4.0',
            'spatie/laravel-permission:^7.0',
            'laravel/wayfinder:^0.1',
        ])) {
            return 1;
        }

        $this->call('vendor:publish', [
            '--provider' => "Spatie\Permission\PermissionServiceProvider",
        ]);

        $this->updatePackageJson(function ($packages) {
            return [
                '@inertiajs/react' => '^3.0',
                '@inertiajs/vite' => '^3.0',
                '@laravel/vite-plugin-wayfinder' => '^0.1',
                '@tailwindcss/vite' => '^4.0',
                '@tanstack/react-query' => '^5',
                '@tanstack/react-table' => '^8',
                '@types/react' => '^19.0',
                '@types/react-dom' => '^19.0',
                '@vitejs/plugin-react' => '^6.0',
                'babel-plugin-react-compiler' => '^1.0',
                'class-variance-authority' => '^0.7',
                'clsx' => '^2',
                'date-fns' => '^3',
                'laravel-vite-plugin' => '^3.0',
                'lucide-react' => '^0.475',
                'prettier' => '^3.3',
                'prettier-plugin-tailwindcss' => '^0.6',
                'react' => '^19.0',
                'react-dom' => '^19.0',
                'react-hook-form' => '^7',
                'sonner' => '^2.0',
                'tailwind-merge' => '^3',
                'tailwindcss' => '^4.0',
                'typescript' => '^5.7',
                'vite' => '^8.0',
                'zod' => '^3',
            ] + $packages;
        }, 'devDependencies');

        $this->updatePackageJson(function ($entries) {
            return [
                'semi' => false,
                'tabWidth' => 2,
                'singleQuote' => true,
                'trailingComma' => 'all',
                'printWidth' => 80,
            ] + $entries;
        }, 'prettier');

        (new Filesystem)->ensureDirectoryExists(app_path('Incrudible'));
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../../stubs/app/Incrudible',
            app_path('Incrudible')
        );

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

        copy(__DIR__ . '/../../stubs/resources/css/incrudible.css', resource_path('css/incrudible.css'));
        copy(__DIR__ . '/../../stubs/vite.config.ts', base_path('vite.config.ts'));
        copy(__DIR__ . '/../../stubs/tsconfig.json', base_path('tsconfig.json'));
        copy(__DIR__ . '/../../stubs/components.json', base_path('components.json'));

        $this->call('wayfinder:generate');

        $this->comment('All done');

        return self::SUCCESS;
    }
}
