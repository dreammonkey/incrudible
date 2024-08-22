<?php

namespace Incrudible\Incrudible\Commands\Crud\Config;

use Illuminate\Support\Str;
use Brick\VarExporter\VarExporter;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Incrudible\Incrudible\Traits\GeneratesFormRules;

class CrudConfigMakeCommand extends GeneratorCommand
{
    use GeneratesFormRules;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:config';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'config';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'crud:config {table : The table name of the CRUD resource.} {parents?* : The parent tables for nested resources (comma-separated).} {--force : Overwrite existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD config file';

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
            ['table', InputArgument::REQUIRED, ' The database table name of the CRUD resource'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The parent table names for nested resources (multiple parents can be specified)'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/crud/config.stub');
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
            : __DIR__ . '/../../../../resources' . $stub;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim(Str::plural($this->argument('table')));
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $name = $this->getNameInput();
        $parents = $this->argument('parents') ?: [];

        if ($parents) {
            $name = implode('.', array_merge($parents, [$name]));
        }

        $path = config_path('incrudible/' . $name . '.php');

        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($path)
        ) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type . ' created successfully.');
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
        $table = $this->getNameInput();

        $stub = $this->files->get($this->getStub());

        return $this->replaceClass($stub, $table);
    }

    protected function replaceClass($stub, $name)
    {
        $metadata = $this->getFormMetaData($name);
        $formRules = $this->augmentFormRules($metadata['rules'], $name);

        $searchable = VarExporter::export(
            collect($this->getFormRules($name, 'string'))->keys()->toArray(),
            VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 2
        );

        $sortable = VarExporter::export(
            $this->getFormFields($name),
            VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 2
        );

        $rules = VarExporter::export(
            $formRules,
            VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 2
        );

        // filter out id, created_at, updated_at
        $formFields = array_filter($metadata['fields'], function ($field) {
            return ! in_array($field['name'], ['id', 'created_at', 'updated_at']);
        });

        $fields = VarExporter::export(
            array_values($formFields),
            VarExporter::TRAILING_COMMA_IN_ARRAY,
            indentLevel: 2
        );

        $model = Str::studly(Str::singular($name));

        $stub = str_replace(
            [
                '{{ model }}',
                '{{ fields }}',
                '{{ rules }}',
                '{{ searchable }}',
                '{{ listable }}',
                '{{ sortable }}',
            ],
            [
                $model,
                $fields,
                $rules,
                $searchable,
                $searchable,
                $sortable,
            ],
            $stub
        );

        return $stub;
    }
}
