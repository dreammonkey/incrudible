<?php

namespace Incrudible\Incrudible\Commands\Crud\Resource;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Incrudible\Incrudible\Traits\GeneratesFormRules;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudResourceMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;
    use GeneratesCruds;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD resource class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the config even if the file already exists'],
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
            ['table', InputArgument::REQUIRED, 'the table name of the CRUD resource'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'the parent table names for nested resources (multiple parents can be specified)'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/crud/resource.stub');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $modelStudly = $this->getModelName();

        $namespace = $this->getDefaultNamespace('/');

        return "{$namespace}\\Http\\Resources\\{$modelStudly}{$this->type}";
    }

    protected function replaceClass($stub, $name)
    {
        $name = $this->getNameInput();
        $table = $this->getTableName();
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $parents = $this->getParents();

        $toArray = $this->generateToArray($table, $parents);

        return str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ toArray }}',
            ],
            [
                $this->getNamespace($name),
                $class,
                $toArray,
            ],
            $stub
        );
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

    protected function generateToArray($table, $parents)
    {
        $metadata = $this->getFormMetaData($table);
        $fieldNames = array_map(fn($field) => $field['name'], $metadata['fields']);

        // Generate the fields array part
        $fieldsString = '';
        foreach ($fieldNames as $field) {
            $fieldsString .= "'{$field}' => \$this->{$field},\n            ";
        }

        // Generate the route parameters
        $routeParams = [];
        foreach ($parents as $parent) {
            $parentRouteKey = Str::singular($parent);
            $routeParams[] = "'{$parentRouteKey}' => \$this->{$parentRouteKey}_id";
        }
        $routeKey = Str::singular($table);
        $routeParams[] = "'{$routeKey}' => \$this->id";
        $routeParamsString = implode(', ', $routeParams);

        // Generate the actions array
        $actionsString = <<<EOT
[
                [
                    'action' => 'show',
                    'url' => incrudible_route('{$this->generateRouteName($table,$parents)}.show', [
                        {$routeParamsString}
                    ]),
                ],
                [
                    'action' => 'edit',
                    'url' => incrudible_route('{$this->generateRouteName($table,$parents)}.edit', [
                        {$routeParamsString}
                    ]),
                ],
                [
                    'action' => 'destroy',
                    'url' => incrudible_route('{$this->generateRouteName($table,$parents)}.destroy', [
                        {$routeParamsString}
                    ]),
                ],
            ]
EOT;

        // Combine fields and actions
        $toArray = <<<EOT
return [
            {$fieldsString}
            'actions' => {$actionsString},
        ];
EOT;

        return $toArray;
    }

    /**
     * Generate the route name based on the table and parents.
     *
     * @param string $table
     * @param array $parents
     * @return string
     */
    protected function generateRouteName($table, $parents)
    {
        $parentsString = !empty($parents) ? implode('.', $parents) . '.' : '';
        return $parentsString . Str::plural(Str::lower($table));
    }
}
