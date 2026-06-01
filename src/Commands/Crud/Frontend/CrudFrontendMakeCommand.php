<?php

namespace Incrudible\Incrudible\Commands\Crud\Frontend;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudFrontendMakeCommand extends GeneratorCommand
{
    use GeneratesCruds;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD typescript component';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'tsx';

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
            ['table', InputArgument::REQUIRED, 'The database table name that the component is created for'],
            ['component', InputArgument::REQUIRED, 'The component name, choose from Index, Create, Edit, Show'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The parent models for nested resources (multiple parents can be specified)'],
        ];
    }

    /**
     * Get the component name.
     */
    protected function getComponentName()
    {
        return Str::ucfirst($this->argument('component'));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $component = $this->getComponentName();
        $modelStudlyPlural = Str::plural($this->getModelName());

        return resource_path("js/Incrudible/Pages/{$modelStudlyPlural}/{$component}.{$this->type}");
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $component = $this->getComponentName();

        return "{$component}.{$this->type}";
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $component = Str::ucfirst($this->argument('component'));
        $stub = "/stubs/js/crud/{$component}.{$this->type}.stub";

        return $this->resolveStubPath($stub);
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        // TODO: nested resources need access to parent models

        $models = $this->getTableName();
        $model = Str::singular($models);
        $Model = $this->getModelName();
        $Models = Str::plural($Model);
        $crudRoute = $this->getRouteName();

        // Get parent models for nested resources
        $parents = $this->getParents();
        $parentData = $this->getParentData($parents, $model);
        $controller = $this->getControllerName($parents);
        $controllerImport = $this->getControllerImport($controller);
        // dd($parentData);

        return str_replace(
            [
                '{{ model }}',
                '{{ models }}',
                '{{ Model }}',
                '{{ Models }}',
                '{{ crudRoute }}',
                '{{ parentImports }}',
                '{{ parentProps }}',
                '{{ parentPropTypes }}',
                '{{ parentRouteParams }}',
                '{{ fullRouteParams }}',
                '{{ routeController }}',
                '{{ routeControllerImport }}',
                '{{ indexRoute }}',
                '{{ createRoute }}',
                '{{ storeRoute }}',
                '{{ updateRoute }}',
            ],
            [
                $model,
                $models,
                $Model,
                $Models,
                $crudRoute,
                $parentData['parentImports'],
                $parentData['parentProps'],
                $parentData['parentPropTypes'],
                $parentData['parentRouteParams'],
                $parentData['fullRouteParams'],
                $controller,
                $controllerImport,
                $this->routeMethodCall($controller, 'index', $parentData['parentRouteParams']),
                $this->routeMethodCall($controller, 'create', $parentData['parentRouteParams']),
                $this->routeMethodCall($controller, 'store', $parentData['parentRouteParams']),
                $this->routeMethodCall($controller, 'update', $parentData['fullRouteParams']),
            ],
            $stub
        );
    }

    private function getControllerName(array $parents): string
    {
        $controller = $this->getModelName();

        if ($parents) {
            $controller = collect($parents)->reduce(function ($carry, $parent) {
                return Str::studly(Str::singular($parent)) . $carry;
            }, $controller);
        }

        return "{$controller}Controller";
    }

    private function getControllerImport(string $controller): string
    {
        $configuredNamespace = config('incrudible.namespace', 'App\\Incrudible');
        $namespace = str_replace('\\', '/', is_string($configuredNamespace) ? $configuredNamespace : 'App\\Incrudible');

        return "@/actions/{$namespace}/Http/Controllers/{$controller}";
    }

    private function routeMethodCall(string $controller, string $method, string $params): string
    {
        return "{$controller}.{$method}({$params})";
    }

    /**
     * Generate the necessary parent data for use in the component.
     *
     * @return array
     */
    protected function getParentData(array $parents, string $model)
    {
        $parentImports = '';
        $parentProps = '';
        $parentPropTypes = '';
        $parentRouteParams = [];

        foreach ($parents as $index => $parent) {
            $parent = Str::singular($parent);
            $Parent = Str::studly($parent);

            $parentImports .= "{$Parent}, ";
            $parentProps .= "{$parent},\n";
            $parentPropTypes .= "{$parent}: Resource<{$Parent}>\n";
            $parentRouteParams[] = "{$parent}.data.id";
        }

        // Add the current model id for full route params
        $fullRouteParams = [...$parentRouteParams, "{$model}.data.id"];

        return [
            'parentImports' => $parentImports,
            'parentProps' => trim($parentProps, "\n"),
            'parentPropTypes' => trim($parentPropTypes, "\n"),
            'parentRouteParams' => count($parentRouteParams) > 0 ? '[' . implode(', ', $parentRouteParams) . ']' : '',
            'fullRouteParams' => '[' . implode(', ', $fullRouteParams) . ']',
        ];
    }
}
