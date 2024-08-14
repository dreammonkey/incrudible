<?php

namespace Incrudible\Incrudible\Commands\Crud;

use Illuminate\Support\Str;
use function Laravel\Prompts\suggest;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

class CrudMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a crud for a given model.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'crud';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the observer already exists'],
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
            ['model', InputArgument::REQUIRED, 'The model that the crud applies to'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return '';
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        if ($input->getArgument('model')) {
            return;
        }

        $model = suggest(
            'What model should this crud apply to?',
            $this->possibleModels(),
        );

        $input->setArgument('model', $model);
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
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model');
        $model = Str::studly(Str::singular($model));

        $this->info("Generating CRUD for model: {$model}");

        $table = Str::plural(Str::lower($model));
        $force = (bool) $this->option('force');

        $namespace = $this->getDefaultNamespace('/');

        $this->call('crud:config', ['table' => $table, '--force' => $force]);
        $this->call('crud:model', ['name' => "$namespace\Models\\$model", '--force' => $force]);
        $this->call('make:resource', ['name' => "$namespace\Http\Resources\\{$model}Resource", '--force' => $force]);
        $this->call('crud:controller', ['table' => $table, '--force' => $force]);
        $this->call('crud:index-request', ['table' => $table, '--force' => $force]);
        // $this->call('crud:show-request', ['table' => $table, '--force' => $force,]);
        $this->call('crud:store-request', ['table' => $table, '--force' => $force]);
        $this->call('crud:update-request', ['table' => $table, '--force' => $force]);
        $this->call('crud:destroy-request', ['table' => $table, '--force' => $force]);
        $this->call('crud:frontend', ['table' => $table]);

        $this->info('There are a few more steps to complete the setup:');
        $this->info('1. Add the following route to your routes/web.php file:');
        $this->comment("Route::resource('{$table}', '{$model}Controller');");
        $this->info('2. Add the following line to your resources/js/types/incrudible.d.ts file:');
        $this->comment("interface {$model} { ... }");
    }
}
