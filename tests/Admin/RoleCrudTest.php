<?php

use App\Incrudible\Models\Admin;
use App\Incrudible\Models\Permission;
use App\Incrudible\Models\Role;

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

it('updates the role permissions with full permission objects', function () {
    $this->actingAs($this->admin, incrudible_guard_name());

    $role = Role::factory()->create();
    $permissions = Permission::factory()->count(3)->create();

    $response = $this->put(incrudible_route('roles.permissions.update', $role), [
        'items' => $permissions->toArray(),
    ]);

    $response->assertStatus(302)
        ->assertSessionHas('success', 'Permissions updated successfully.');

    $this->assertCount(3, $role->refresh()->permissions);
    foreach ($permissions as $permission) {
        $this->assertTrue($role->permissions->contains($permission));
    }
});
