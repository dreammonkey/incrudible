<?php

namespace Incrudible\Incrudible\Commands;

use Brick\VarExporter\VarExporter;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesFormRules;

class CrudResourceControllerMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-controller {table : The table name of the CRUD resource.} {--force : Overwrite existing files.}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD resource controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/controller.crud.stub');
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
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $table = trim($this->argument('table'));

        $model_singular = ucfirst(Str::singular($table));

        $namespace = $this->getDefaultNamespace('/');

        return "{$namespace}\\Http\\Controllers\\{$model_singular}{$this->type}";
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        $table = trim($this->argument('table'));

        $model_singular = Str::singular($table);
        $model_singular_uc_first = ucfirst($model_singular);
        $model_plural = Str::plural($table);
        $model_plural_uc_first = ucfirst($model_plural);

        $searchableFields = collect($this->getFormRules($model_plural, 'string'))
            ->keys()
            ->toArray();

        $searchableFields = VarExporter::export(
            $searchableFields,
            VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 8
        );

        return str_replace(
            [
                '{{ namespace }}',
                '{{ model_singular }}',
                '{{ model_singular_uc_first }}',
                '{{ model_plural }}',
                '{{ model_plural_uc_first }}',
                '{{ searchableFields }}',
            ],
            [
                $this->getNamespace($name),
                $model_singular,
                $model_singular_uc_first,
                $model_plural,
                $model_plural_uc_first,
                $searchableFields,
            ],
            $stub
        );
    }
}
