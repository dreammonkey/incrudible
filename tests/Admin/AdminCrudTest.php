<?php

use App\Incrudible\Models\Admin;
use App\Incrudible\Models\Role;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

it('prevents guests from accessing any of the admin crud routes', function () {
    $this->get(incrudible_route('admins.index'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('admins.create'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('admins.edit', $this->admin))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->get(incrudible_route('admins.show', $this->admin))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->post(incrudible_route('admins.store'))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->put(incrudible_route('admins.update', $this->admin))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));

    $this->delete(incrudible_route('admins.destroy', $this->admin))
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('auth.login'));
});

it('renders the admin crud index', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('admins.index'))
        ->assertStatus(200);
});

it('returns the admin crud index as json', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->getJson(incrudible_route('admins.index'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ])
        ->assertJsonFragment([
            'username' => $admin->username,
            'email' => $admin->email,
        ]);
});

it('renders the admin crud create', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('admins.create'))
        ->assertStatus(200);
});

it('renders the admin crud edit', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('admins.edit', $this->admin))
        ->assertStatus(200);

    // TODO: Assert Inertia props
});

it('renders the admin crud show', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('admins.show', $this->admin))
        ->assertStatus(200);

    // TODO: Assert Inertia props
});

it('can create a new admin', function () {

    $newAdmin = [
        'username' => 'john.doe',
        'email' => 'john.doe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->actingAs($this->admin, incrudible_guard_name())
        ->post(incrudible_route('admins.store'), $newAdmin)
        ->assertStatus(302)
        ->assertRedirect(incrudible_route('admins.show', 2))
        ->assertSessionHas('success', 'Admin created successfully.');

    $this->assertDatabaseHas('admins', [
        'username' => 'john.doe',
        'email' => 'john.doe@example.com',
    ]);
});

it('can update an existing admin', function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->putJson(incrudible_route('admins.update', $this->admin), [
            'username' => 'John Update',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('admins', [
        'username' => 'John Update',
    ]);
});

it('can delete an existing admin', function () {
    $otherAdmin = Admin::factory()->create();

    $this->actingAs($this->admin, incrudible_guard_name())
        ->delete(incrudible_route('admins.destroy', $otherAdmin))
        ->assertStatus(302);

    $this->assertDatabaseMissing('admins', [
        'id' => $otherAdmin->id,
    ]);
});

it(('prevents admins from deleting themselves'), function () {
    $this->actingAs($this->admin, incrudible_guard_name())
        ->delete(incrudible_route('admins.destroy', $this->admin))
        ->assertStatus(403);

    $this->assertDatabaseHas('admins', [
        'id' => $this->admin->id,
    ]);
});

it('updates the roles for an admin with full role objects', function () {
    $this->actingAs($this->admin, incrudible_guard_name());

    $admin = Admin::factory()->create();
    $roles = Role::factory()->count(3)->create();

    $response = $this->put(incrudible_route('admins.roles.update', $admin), [
        'items' => $roles->toArray(),
    ]);

    $response->assertStatus(302)
        ->assertSessionHas('success', 'Roles updated successfully.');

    $this->assertCount(3, $admin->refresh()->roles);
    foreach ($roles as $role) {
        $this->assertTrue($admin->roles->contains($role));
    }
});
