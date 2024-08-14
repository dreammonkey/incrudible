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

it('allows an admin to create a role with permissions', function () {
    $this->markTestIncomplete('This test has not been implemented yet.');

    $this->actingAs($this->admin, incrudible_guard_name());

    $permissions = Permission::factory()->count(3)->create();

    $response = $this->post(incrudible_route('roles.store'), [
        'name' => 'Test Role',
        'permissions' => $permissions->toArray(),
    ]);

    $response->assertStatus(302)
        ->assertSessionHas('success', 'Role created successfully.');

    $role = Role::where('name', 'Test Role')->first();
    $this->assertNotNull($role);
    $this->assertCount(3, $role->permissions);
    foreach ($permissions as $permission) {
        $this->assertTrue($role->permissions->contains($permission));
    }
});

it('allows an admin to delete a role', function () {
    $this->actingAs($this->admin, incrudible_guard_name());

    $role = Role::factory()->create();

    $response = $this->delete(incrudible_route('roles.destroy', $role));

    $response->assertStatus(302)
        ->assertSessionHas('success', 'Role deleted successfully.');

    $this->assertNull(Role::find($role->id));
});

it('validates the required fields when creating a role', function () {
    $this->actingAs($this->admin, incrudible_guard_name());

    $response = $this->post(incrudible_route('roles.store'), [
        'name' => '', // Empty name to trigger validation error
    ]);

    $response->assertSessionHasErrors(['name']);
});

// it('updates the role name and permissions', function () {
//     $this->actingAs($this->admin, incrudible_guard_name());

//     $role = Role::factory()->create();
//     $permissions = Permission::factory()->count(3)->create();

//     $response = $this->put(incrudible_route('roles.update', $role), [
//         'name' => 'Updated Role Name',
//         'permissions' => $permissions->toArray(),
//     ]);

//     $response->assertStatus(302)
//         ->assertSessionHas('success', 'Role updated successfully.');

//     $role = $role->refresh();
//     $this->assertEquals('Updated Role Name', $role->name);
//     $this->assertCount(3, $role->permissions);
//     foreach ($permissions as $permission) {
//         $this->assertTrue($role->permissions->contains($permission));
//     }
// });
