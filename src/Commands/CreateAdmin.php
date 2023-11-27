<?php

namespace Incrudible\Incrudible\Commands;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;
use Incrudible\Incrudible\Facades\Incrudible;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incrudible:admin
                            {--N|username= : The admin\'s username}
                            {--E|email= : The admin\'s email address}
                            {--P|password= : The admin\'s password}
                            {--R|role= : The admin\'s role}
                            {--encrypt=true : Encrypt admin\'s password if it\'s plain text ( true by default )}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // check if the config file has already been published
        if (Incrudible::configNotPublished()) {
            return $this->warn(
                'Please publish the config file first by running \'php artisan vendor:publish --provider="Incrudible\IncrudibleServiceProvider" --tag=config\''
            );
        }

        $this->info('Creating new admin user...');

        $git_username = trim(Process::run("git config user.name")->output());
        $git_email = trim(Process::run("git config user.email")->output());

        if (!$username = $this->option('username')) {
            $username = $this->ask('Username', $git_username);
        }

        if (!$email = $this->option('email')) {
            $email = $this->ask('Email', $git_email);
        }

        if (!$password = $this->option('password')) {
            $password = $this->secret('Password (leave blank to autogenerate)');
        }

        if ($this->option('encrypt')) {
            $password = strlen($password) ? $password : Str::random(12);
            $password_hashed = Hash::make($password);
        }

        // if (!$role = $this->option('role')) {
        //     $role = $this->choice('Role', ['admin', 'super-admin', 'custom']);

        //     if ($role === 'custom') {
        //         $role = $this->ask('role name:');
        //     }
        // }

        $model = config('incrudible.auth.user_model_fqn', \App\Incrudible\Models\Admin::class);

        $admin = new $model();
        $admin->username = $username;
        $admin->email = $email;
        $admin->password = $password_hashed;


        try {
            $admin->save();

            event(new Registered($admin));

            $this->info('Successfully created new admin user:');
            $this->info("- username: {$username}");
            $this->info("- email: {$email}");
            $this->info("- password: {$password}");
        } catch (\Throwable $th) {

            $this->error('Something went wrong while saving admin user.');

            // dd($th->getCode());
            if (intval($th->getCode()) === 23000) {
                $this->warn('Most likely a admin user already exists with this email address.');
            }
        }
    }
}
