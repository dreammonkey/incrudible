<?php

use App\Incrudible\Models\Admin;
use Incrudible\Incrudible\Facades\Incrudible;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('displays the login page', function () {
    get(incrudible_route('auth.login'))
        ->assertStatus(200);
});

it('allows users to login', function () {
    $admin = Admin::factory()->create([
        'password' => 'password',
    ]);

    post(incrudible_route('auth.login'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(incrudible_route('dashboard'));
    assertAuthenticatedAs($admin, incrudible_guard_name());
    expect(Incrudible::check())->toBeTrue();
});

it('allows authenticated admins to access the dashboard', function () {
    $admin = Admin::factory()->create();
    actingAs($admin, incrudible_guard_name())
        ->get(incrudible_route('dashboard'))
        ->assertStatus(200);
});

it('redirects unauthenticated users to the login page', function () {
    get(incrudible_route('dashboard'))
        ->assertRedirect();
});

it('denies users to login with invalid credentials', function () {
    $admin = Admin::factory()->create();

    post(incrudible_route('auth.login'), [
        'email' => $admin->email,
        'password' => 'not my password',
    ])->assertSessionHasErrors();
    assertGuest();
    expect(Incrudible::check())->toBeFalse();
});
