<?php

namespace Incrudible\Incrudible\Commands;

use Brick\VarExporter\VarExporter;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Incrudible\Incrudible\Traits\GeneratesFormRules;

class CrudStoreRequestMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-store-request {table : The table name of the CRUD resource.} {--force : Overwrite existing files.}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD store request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // TODO: store is identical to update
        return $this->resolveStubPath('/stubs/request.store.stub');
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
            : __DIR__.'/../../resources'.$stub;
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

        $modelName = ucfirst(Str::singular($table));

        $namespace = $this->getDefaultNamespace('/');

        return "{$namespace}\\Http\\Requests\\{$modelName}\\Store{$modelName}{$this->type}";
    }

    /**
     * Replace the rulesFields.
     *
     * @param  string  $stub
     * @param  array  $rulesFields
     * @return $this
     */
    protected function replaceRulesFields(&$stub, $rulesFields)
    {
        $fields = VarExporter::export(
            $rulesFields,
            VarExporter::ADD_RETURN | VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 2
        );

        $stub = str_replace(['{{ rulesFields }}', '{{rulesFields}}'], $fields, $stub);

        return $this;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $table = trim($this->argument('table'));
        $stub = $this->files->get($this->getStub());

        $fields = $this->getFormRules($table);
        $fields = $this->augmentFormRules($fields, $table);

        return $this->replaceNamespace($stub, $name)
            ->replaceRulesFields($stub, $fields)
            ->replaceClass($stub, $name);
    }
}
