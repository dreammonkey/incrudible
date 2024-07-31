<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrudFrontEndMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-frontend {table : The table name of the CRUD resource.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all CRUD frontend typescript files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::lower($this->argument('table'));

        $files = ['Index', 'Create', 'Edit', 'Show'];
        foreach ($files as $file) {
            $this->generateFile($name, $file);
        }

        $this->info('CRUD files created successfully.');
    }

    protected function generateFile($name, $fileType)
    {
        $instanceSingular = Str::singular($name);
        $instancePlural = Str::plural($name);
        $modelName = ucfirst($instanceSingular);
        $modelNamePlural = Str::plural($modelName);

        $stubPath = __DIR__ . "/../../resources/stubs/js/crud/{$fileType}.tsx.stub";
        $targetPath = resource_path("js/Incrudible/Pages/{$modelNamePlural}/{$fileType}.tsx");

        if (!File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        $content = File::get($stubPath);
        $content = str_replace(
            [
                '{{ instanceSingular }}',
                '{{ instancePlural }}',
                '{{ modelName }}',
                '{{ modelNamePlural }}',
            ],
            [
                $instanceSingular,
                $instancePlural,
                $modelName,
                $modelNamePlural,
            ],
            $content
        );

        File::put($targetPath, $content);
    }
}
