<?php

use App\Incrudible\Models\Admin;
use App\Incrudible\Models\Permission;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

it('prevents guests from accessing any of the permission crud routes', function () {
    $this->get(incrudible_route('permissions.index'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('permissions.create'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('permissions.edit', Permission::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('permissions.show', Permission::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->post(incrudible_route('permissions.store'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->put(incrudible_route('permissions.update', Permission::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->delete(incrudible_route('permissions.destroy', Permission::factory()->create()))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));
});

it('renders the permission crud index', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('permissions.index'))
        ->assertStatus(200);
});

it('returns the permission crud index as json', function () {
    $permission = Permission::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->getJson(incrudible_route('permissions.index'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ])
        ->assertJsonFragment([
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ]);
});

it('renders the permission crud create', function () {
    $permission = Permission::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('permissions.create'))
        ->assertStatus(200);
});

it('renders the permission crud edit', function () {
    $permission = Permission::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('permissions.edit', $permission))
        ->assertStatus(200);

    // TODO: Assert Inertia props
});

it('renders the permission crud show', function () {
    $permission = Permission::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('permissions.show', $permission))
        ->assertStatus(200);

    // TODO: Assert Inertia props
});

it('can create a new permission', function () {
    $permission = Permission::factory()->make();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->postJson(incrudible_route('permissions.store'), $permission->toArray())
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('permissions.show', Permission::first()))
        ->assertSessionHas('success', 'Permission created successfully.');

    $this->assertDatabaseHas('permissions', $permission->toArray());
});

it('can update a permission', function () {
    $permission = Permission::factory()->create();
    $newPermission = Permission::factory()->make();

    $response = $this->actingAs($this->admin, incrudible_guard_name())
        ->putJson(incrudible_route('permissions.update', $permission), $newPermission->toArray())
        ->assertStatus(302)
        ->assertSessionHas('success', 'Permission updated successfully.');

    $this->assertDatabaseHas('permissions', $newPermission->toArray());
});

it('can delete a permission', function () {
    $permission = Permission::factory()->create();

    // Assert that the permission exists in the database
    $this->assertDatabaseHas('permissions', [
        'id' => $permission->id,
        'name' => $permission->name,
        'guard_name' => $permission->guard_name,
    ]);

    $this->actingAs($this->admin, incrudible_guard_name())
        ->delete(incrudible_route('permissions.destroy', $permission))
        ->assertStatus(302)
        ->assertSessionHas('success', 'Permission deleted successfully.');

    // Assert that the permission no longer exists in the database
    $this->assertDatabaseMissing('permissions', [
        'id' => $permission->id,
        'name' => $permission->name,
        'guard_name' => $permission->guard_name,
    ]);
});
