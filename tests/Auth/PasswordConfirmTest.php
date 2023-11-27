<?php

use App\Incrudible\Models\Admin;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Admin::factory()->create([
        'password' => 'password',
    ]);
});

it('displays the password confirmation page', function () {

    actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('password.confirm'))
        ->assertStatus(200);
});

it('redirects a secured page to the password confirmation page', function () {
    actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('settings'))
        ->assertRedirect(incrudible_route('password.confirm'));
});

it('allows admins to confirm their passwords', function () {

    actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('settings'));
    actingAs($this->admin, incrudible_guard_name())
        ->post(incrudible_route('password.confirm.post'), [
            'password' => 'password',
        ])
        ->assertRedirect(incrudible_route('settings'))
        ->assertSessionHasNoErrors();
});
