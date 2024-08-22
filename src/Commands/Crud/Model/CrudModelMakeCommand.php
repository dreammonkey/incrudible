<?php

namespace Incrudible\Incrudible\Commands\Crud\Model;

use Brick\VarExporter\VarExporter;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Incrudible\Incrudible\Traits\GeneratesFormRules;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudModelMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;
    use GeneratesCruds;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['table', InputArgument::REQUIRED, 'The name of the model class'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The parent model names for nested resources (multiple parents can be specified)'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the model even if the file already exists'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/crud/model.stub');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $modelStudly = $this->getModelName('table');

        $namespace = $this->getDefaultNamespace('/');

        return "{$namespace}\\Models\\{$modelStudly}";
    }

    protected function replaceClass($stub, $name)
    {
        // dd("dd", $name);
        // $filename = $this->getNameInput();
        // dd($filename);
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        // dd($class);
        // $parents = $this->getParents();

        $fillable = $this->generateFillableAttributes($class);

        return str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ relationships }}',
                '{{ fillable }}',
            ],
            [
                $this->getNamespace($name),
                $class,
                "/** TODO: Define relationships */",
                VarExporter::export(array_values($fillable), VarExporter::TRAILING_COMMA_IN_ARRAY, indentLevel: 1),
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

    /**
     * Generate the fillable attributes for the model.
     *
     * @return array
     */
    protected function generateFillableAttributes()
    {
        // Assume the fillable attributes are inferred from the table schema
        $metadata = $this->getFormMetaData($this->getTableName('table'));
        $fieldNames = array_map(fn($field) => $field['name'], $metadata['fields']);

        $fillable = array_filter($fieldNames, function ($field) {
            return !in_array($field, ['id', 'created_at', 'updated_at']);
        });

        return $fillable;
    }
}
