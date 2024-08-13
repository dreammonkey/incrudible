<?php

use App\Incrudible\Models\Role;
use App\Incrudible\Models\Admin;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

it('prevents guests from accessing any of the role crud routes', function () {
    $role = Role::factory()->create();

    $this->get(incrudible_route('roles.index'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.create'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.edit', $role))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.show', $role))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->post(incrudible_route('roles.store'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->put(incrudible_route('roles.update', $role))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->delete(incrudible_route('roles.destroy', $role))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));
});

it('renders the role crud index', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('roles.index'))
        ->assertStatus(200);
});
