<?php

use App\Incrudible\Models\Admin;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

it('allows admins to update their password', function () {
    $admin = Admin::factory()->create([
        'password' => 'password',
    ]);

    actingAs($admin, incrudible_guard_name())
        ->from(incrudible_route('profile.edit'))
        ->put(incrudible_route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(incrudible_route('profile.edit'));

    expect(Hash::check('new-password', $admin->refresh()->password))->toBeTrue();
});
