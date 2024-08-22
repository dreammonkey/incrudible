<?php

namespace Incrudible\Incrudible\Commands\Crud\Request;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudRequestMakeCommand extends GeneratorCommand
{
    use GeneratesCruds;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD request (index, store, update, destroy, show)';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the crud already exists'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['table', InputArgument::REQUIRED, 'The database table name that the CRUD applies to'],
            ['method', InputArgument::REQUIRED, 'The CRUD method (index, store, update, destroy, show)'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The parent models for nested resources (multiple parents can be specified)'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $method = strtolower($this->argument('method'));

        switch ($method) {
            case 'index':
                return $this->resolveStubPath('/stubs/crud/request.index.stub');
            case 'store':
                return $this->resolveStubPath('/stubs/crud/request.store.stub');
            case 'update':
                return $this->resolveStubPath('/stubs/crud/request.update.stub');
            case 'destroy':
                return $this->resolveStubPath('/stubs/crud/request.destroy.stub');
            case 'show':
                return $this->resolveStubPath('/stubs/crud/request.show.stub');
            default:
                throw new \InvalidArgumentException("Invalid method: {$method}. Valid methods are index, store, update, destroy, show.");
        }
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $modelName = $this->getModelName();
        $method = ucfirst(strtolower($this->argument('method')));

        $namespace = $this->getDefaultNamespace('/');

        // For "index", pluralize the model name
        if ($method === 'Index') {
            $modelNamePlural = Str::pluralStudly($modelName);
            return "{$namespace}\\Http\\Requests\\{$modelName}\\Get{$modelNamePlural}Request";
        }

        // For other methods (store, update, destroy, show)
        return "{$namespace}\\Http\\Requests\\{$modelName}\\{$method}{$modelName}{$this->type}";
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
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        $namespace = $this->getNamespace($name);
        $class = str_replace($namespace . '\\', '', $name);
        $table = $this->getTableName();

        $crudRoute = $this->getRouteName();

        $model = Str::singular($table);
        $models = Str::plural($table);

        $parent = $this->getParents()[0] ?? null;
        $parents = Str::plural($parent);

        return str_replace(
            [
                '{{ class }}',
                '{{ namespace }}',
                '{{ model }}',
                '{{ models }}',
                '{{ parents }}',
                '{{ crudRoute }}',
            ],
            [
                $class,
                $namespace,
                $model,
                $models,
                $parents,
                $crudRoute,
            ],
            $stub
        );
    }
}
