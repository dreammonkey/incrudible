<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Str;

trait GeneratesCruds
{
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('incrudible.namespace');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . '/../../resources' . $stub;
    }

    /**
     * Format model argument to studly case.
     *
     * @return array
     */
    protected function getModelName($argument = 'table')
    {
        return Str::studly(Str::singular($this->argument($argument)));
    }

    /**
     * Format the database table name
     */
    protected function getTableName($argument = 'table')
    {
        return Str::plural(Str::lower($this->getModelName($argument)));
    }

    /**
     * Create the crud route name.
     */
    protected function getRouteName()
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
     *
     * @return array
     */
    protected function getParents()
    {
        return array_map(function ($parent) {
            return Str::plural(Str::lower($parent));
        }, $this->argument('parents') ?? []);
    }
}
