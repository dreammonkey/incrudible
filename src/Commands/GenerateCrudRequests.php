<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Console\Command;

class GenerateCrudRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-requests {table : The table name of the CRUD resource.} {--force : Overwrite existing files.}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all CRUD resource requests';

    public function handle()
    {
        $table = $this->argument('table');
        $force = (bool) $this->option('force');

        $this->call('make:crud-index-request', ['table' => $table, '--force' => $force]);
        // $this->call('make:crud-show-request', ['table' => $table, '--force' => $force,]);
        $this->call('make:crud-store-request', ['table' => $table, '--force' => $force]);
        $this->call('make:crud-update-request', ['table' => $table, '--force' => $force]);
        // $this->call('make:crud-destroy-request', ['table' => $table, '--force' => $force,]);
    }
}
