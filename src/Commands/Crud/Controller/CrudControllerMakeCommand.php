<?php

namespace Incrudible\Incrudible\Commands\Crud\Controller;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Incrudible\Incrudible\Traits\GeneratesFormRules;

class CrudControllerMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;
    use GeneratesCruds;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD resource controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the crud already exists'],
            ['relation', null, InputOption::VALUE_REQUIRED, 'Specify the type of relation controller (default, nested, BelongsToMany)'],
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
        // $parents = $this->getParents();
        // $stub = count($parents) > 0
        //     ? '/stubs/crud/controller.nested.stub'
        //     : '/stubs/crud/controller.stub';
        // // dd($stub);
        // return $this->resolveStubPath($stub);
        $relationType = $this->option('relation') ?: 'default';
        $stub = match ($relationType) {
            'nested' => '/stubs/crud/controller.nested.stub',
            'BelongsToMany' => '/stubs/crud/controller.belongs-to-many.stub',
            default => '/stubs/crud/controller.stub',
        };

        return $this->resolveStubPath($stub);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $modelStudly = $this->getModelName();
        $parents = $this->argument('parents') ?: [];

        if ($parents) {
            $modelStudly = collect($parents)->reduce(function ($carry, $parent) {
                return Str::studly(Str::singular($parent)) . $carry;
            }, $modelStudly);
        }

        $namespace = $this->getDefaultNamespace('/');

        return "{$namespace}\\Http\\Controllers\\{$modelStudly}{$this->type}";
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        $namespace = $this->getNamespace($name);
        $class = str_replace($namespace . '\\', '', $name);
        $table = $this->getTableName();
        $crudRoute = $this->getRouteName();

        $model = Str::singular($table);
        $Model = $this->getModelName();
        $models = Str::plural($table);
        $Models = Str::studly($models);
        $ModelRequest = $Model . 'Request';

        // $parent = sizeof($this->getParents()) > 0 ? Str::singular($this->getParents()[0]) : null;
        // $Parent = $parent ? Str::studly(Str::singular($parent)) : null;
        // $parents = sizeof($this->getParents()) > 0 ? Str::plural($parent) : null;
        // $Parents = $Parent ? Str::studly($parents) : null;
        // $ParentRequest = $Parent ? $Parent . 'Request' : null;

        // Get parent models for nested resources
        $parents = $this->getParents();
        $parentData = $this->getParentData($parents, $model);

        // dd($parentData);

        return str_replace(
            [
                '{{ class }}',
                '{{ namespace }}',
                '{{ model }}',
                '{{ Model }}',
                '{{ models }}',
                '{{ Models }}',
                '{{ ModelRequest }}',
                '{{ crudRoute }}',
                // '{{ parent }}',
                // '{{ Parent }}',
                // '{{ parents }}',
                // '{{ Parents }}',
                // '{{ ParentRequest }}',
                '{{ parentImports }}',
                '{{ parentArgs }}',
                '{{ parentProps }}',
                '{{ parentRouteParams }}',
                // '{{ fullRouteParams }}',
                '{{ directParent }}',
                '{{ directParentInstance }}',
            ],
            [
                $class,
                $namespace,
                $model,
                $Model,
                $models,
                $Models,
                $ModelRequest,
                $crudRoute,
                // $parent,
                // $Parent,
                // $parents,
                // $Parents,
                // $ParentRequest,
                $parentData['parentImports'],
                $parentData['parentArgs'],
                $parentData['parentProps'],
                $parentData['parentRouteParams'],
                // $parentData['fullRouteParams'],
                $parentData['directParent'],
                $parentData['directParentInstance'],
            ],
            $stub
        );
    }

    /**
     * Generate the necessary parent data for use in the component.
     *
     * @param array $parents
     * @param string $model
     * @return array
     */
    protected function getParentData(array $parents, string $model)
    {
        $parentImports = '';
        $parentArgs = '';
        $parentProps = '';
        $parentRouteParams = '';
        $directParent = null;
        $directParentInstance = null;

        foreach ($parents as $index => $parent) {
            $parent = Str::singular($parent);
            $Parent = Str::studly($parent);

            $parentImports .= "use App\Incrudible\Models\\{$Parent};\n";
            $parentArgs .= "{$Parent} \${$parent}, ";
            $parentProps .= "'{$parent}' => \${$parent}->toResource(),\n";
            $parentRouteParams .= "\${$parent}->id, ";
            $directParent = $Parent;
            $directParentInstance = "\$$parent";
        }

        $fullRouteParams = rtrim($parentRouteParams, ', ') . ", \${$model}->id";

        return [
            'parentImports' => trim($parentImports, "\n"),
            'parentArgs' => trim($parentArgs, ", "),
            'parentProps' => trim($parentProps, "\n"),
            'parentRouteParams' => $parentRouteParams,
            'directParent' => $directParent,
            'directParentInstance' => $directParentInstance,
        ];
    }
}
