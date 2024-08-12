<?php

use App\Incrudible\Models\Role;

it('prevents guests from accessing any of the role crud routes', function () {
    $this->get(incrudible_route('roles.index'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.create'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.edit', Role::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('roles.show', Role::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->post(incrudible_route('roles.store'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->put(incrudible_route('roles.update', Role::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->delete(incrudible_route('roles.destroy', Role::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));
});
