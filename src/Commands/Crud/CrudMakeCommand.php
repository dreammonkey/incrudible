<?php

namespace Incrudible\Incrudible\Commands\Crud;

use function Laravel\Prompts\suggest;
use Illuminate\Console\GeneratorCommand;
use Incrudible\Incrudible\Traits\GeneratesCruds;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrudMakeCommand extends GeneratorCommand
{
    use GeneratesCruds;

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
            ['force', 'f', InputOption::VALUE_NONE, 'create the class even if the crud already exists'],
            ['nested', null, InputOption::VALUE_NONE, 'indicate if the resource is nested under one or more resources'],
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
            ['model', InputArgument::REQUIRED, 'the model that the CRUD applies to'],
            ['parents', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'the parent models for nested resources (multiple parents can be specified)'],
            // ['relations', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'the relations for the model'],
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

        // Prompt for the model if not provided
        if (!$input->getArgument('model')) {
            $model = suggest(
                'What model should this CRUD apply to?',
                $this->possibleModels()
            );
            $input->setArgument('model', $model);
        }

        // // Step 2: Ask if the model has any relations
        // $relations = [];
        // while (true) {
        //     $hasRelation = $this->choice(
        //         'Do you want to specify a relationship?',
        //         ['Yes', 'No'],
        //         'No'
        //     ) == 'Yes';
        //     if (!$hasRelation) {
        //         break;
        //     }

        //     // Step 3: Ask for the related model and type of relation
        //     $relatedModel = suggest(
        //         'What is the related model?',
        //         $this->possibleModels()
        //     );

        //     $relationType = $this->choice(
        //         'What type of relation is this?',
        //         ['HasMany', 'BelongsTo'],
        //         'HasMany'
        //     );

        //     // Store the relation details
        //     $relations[] = [
        //         'type' => $relationType,
        //         'model' => Str::studly(Str::singular($relatedModel)),
        //         'table' => Str::plural(Str::lower($relatedModel))
        //     ];
        // }

        // // Store relations in an option for later use
        // $input->setArgument('relations', $relations);

        // Handle nested resources
        $parents = $input->getArgument('parents') ?: [];
        while (true) {
            $nested = $this->choice('Does this model have a parent model?', ['Yes', 'No'], 'No') == 'Yes';
            if ($nested) {
                $parentModel = suggest(
                    'What is the parent model?',
                    $this->possibleModels()
                );
                $parents[] = $parentModel;
            } else {
                break;
            }
        }

        // Set the parent models as an argument if there are any
        if (!empty($parents)) {
            $input->setArgument('parents', $parents);
            $input->setOption('nested', true);
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->getModelName('model');
        $table = $this->getTableName('model');
        $parents = $this->getParents();
        $nested = $this->option('nested');
        $force = (bool) $this->option('force');
        // $relations = $this->argument('relations');
        // dd($relations);
        // $parents = [];

        // Config
        $this->info("Generating config for model: {$model}");
        $this->call('crud:config', ['table' => $table, 'parents' => $parents, '--force' => $force]);
        // Model
        $this->info("Generating model for model: {$model}");
        $this->call('crud:model', ['table' => $table, 'parents' => $parents, '--force' => $force]);
        // Resource
        $this->info("Generating resource for model: {$model}");
        $this->call('crud:resource', ['table' => $table, 'parents' => $parents, '--force' => $force]);
        // Controller
        $type = $nested ? 'nested' : 'default';
        $this->info("Generating controller for model: {$model}");
        $this->call('crud:controller', ['table' => $table, 'parents' => $parents, '--relation' => $type, '--force' => $force]);
        // IndexRequest
        $this->info("Generating IndexRequest for model: {$model}");
        $this->call('crud:request', ['table' => $table, 'method' => 'index', 'parents' => $parents, '--force' => $force]);
        // ShowRequest
        $this->info("Generating ShowRequest for model: {$model}");
        $this->call('crud:request', ['table' => $table, 'method' => 'show', 'parents' => $parents, '--force' => $force,]);
        // StoreRequest
        $this->info("Generating StoreRequest for model: {$model}");
        $this->call('crud:request', ['table' => $table, 'method' => 'store', 'parents' => $parents, '--force' => $force]);
        // UpdateRequest
        $this->info("Generating UpdateRequest for model: {$model}");
        $this->call('crud:request', ['table' => $table, 'method' => 'update', 'parents' => $parents, '--force' => $force]);
        // DestroyRequest
        $this->info("Generating DestroyRequest for model: {$model}");
        $this->call('crud:request', ['table' => $table, 'method' => 'destroy', 'parents' => $parents, '--force' => $force]);
        // Frontend Index
        $this->info("Generating Frontend Index for model: {$model}");
        $this->call('crud:frontend', ['table' => $table, 'component' => 'Index', 'parents' => $parents, '--force' => $force]);
        // Frontend Create
        $this->info("Generating Frontend Create for model: {$model}");
        $this->call('crud:frontend', ['table' => $table, 'component' => 'Create', 'parents' => $parents, '--force' => $force]);
        // Frontend Edit
        $this->info("Generating Frontend Edit for model: {$model}");
        $this->call('crud:frontend', ['table' => $table, 'component' => 'Edit', 'parents' => $parents, '--force' => $force]);
        // Frontend Show
        $this->info("Generating Frontend Show for model: {$model}");
        $this->call('crud:frontend', ['table' => $table, 'component' => 'Show', 'parents' => $parents, '--force' => $force]);

        $this->info('There are a few more steps to complete the setup:');
        $this->info('1. Add the following route to your routes/web.php file:');
        $this->comment("Route::resource('{$table}', {$model}Controller::class);");
        $this->info('2. Add the following line to your resources/js/types/incrudible.d.ts file:');
        $this->comment("interface {$model} { ... }");
        $this->info('3. Add the necessary relationships to your models.');
        $this->info('4. Add the necessary relationships to your crud\'s config file.');
        $this->info('5. (optional) Create a pull request to make any of the aforementioned steps automatic.');
    }
}
