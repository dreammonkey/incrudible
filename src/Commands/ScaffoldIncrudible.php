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
        // Install Inertia...
        if (!$this->requireComposerPackages([
            'inertiajs/inertia-laravel:^0.6.8',
            'laravel/sanctum:^3.2',
            'tightenco/ziggy:^1.0',
        ])) {
            return 1;
        }

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@headlessui/react' => '^1.4.2',
                '@inertiajs/react' => '^1.0.0',
                '@tailwindcss/forms' => '^0.5.3',
                '@vitejs/plugin-react' => '^4.0.3',
                'autoprefixer' => '^10.4.12',
                'postcss' => '^8.4.18',
                'tailwindcss' => '^3.2.1',
                'react' => '^18.2.0',
                'react-dom' => '^18.2.0',
                'class-variance-authority' => '^0.7.0',
                'clsx' => '^2.1.0',
                'lucide-react' => '^0.368.0',
                'tailwind-merge' => '^2.2.2',
                'tailwindcss-animate' => '^1.0.7',
                // typescript
                '@types/node' => '^18.13.0',
                '@types/react' => '^18.0.28',
                '@types/react-dom' => '^18.0.10',
                '@types/ziggy-js' => '^1.3.2',
                'typescript' => '^5.0.2',
            ] + $packages;
        });

        // return self::SUCCESS;

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
