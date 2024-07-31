<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;

class CrudModelMakeCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:crud-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CRUD Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/model.crud.stub');
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
}
