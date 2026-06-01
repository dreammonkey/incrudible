<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Str;

trait GeneratesCruds
{
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        $namespace = config('incrudible.namespace', 'App\\Incrudible');

        return is_string($namespace) ? $namespace : 'App\\Incrudible';
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . '/../../resources' . $stub;
    }

    /**
     * Format model argument to studly case.
     */
    protected function getModelName($argument = 'table'): string
    {
        $value = $this->argument($argument);

        if (is_array($value)) {
            $value = reset($value) ?: '';
        }

        return Str::studly(Str::singular((string) $value));
    }

    /**
     * Format the database table name
     */
    protected function getTableName($argument = 'table'): string
    {
        return Str::plural(Str::lower($this->getModelName($argument)));
    }

    /**
     * Create the crud route name.
     */
    protected function getRouteName(): string
    {
        $parents = $this->getParents();

        if ($parents) {
            $crudRoute = collect($parents)->reduce(function ($carry, $parent) {
                return $parent . '.' . $carry;
            }, $this->getTableName());
        } else {
            $crudRoute = $this->getTableName();
        }

        return $crudRoute;
    }

    /**
     * Format parents argument to array of pluralized lowercase strings.
     */
    protected function getParents(): array
    {
        $parents = $this->argument('parents') ?? [];

        if (! is_array($parents)) {
            $parents = [$parents];
        }

        return array_map(function ($parent) {
            return Str::plural(Str::lower((string) $parent));
        }, $parents);
    }
}
