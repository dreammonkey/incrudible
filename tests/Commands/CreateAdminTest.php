<?php

use function Pest\Laravel\artisan;

it('creates an admin via artisan command', function () {
    artisan('incrudible:admin')
        ->expectsQuestion('Username', 'john.doe')
        ->expectsQuestion('Email', 'john.doe@me.com')
        ->expectsQuestion('Password (leave blank to autogenerate)', 'abcd1234')
        ->assertExitCode(0);

    $this->assertDatabaseCount('admins', 1);
});
