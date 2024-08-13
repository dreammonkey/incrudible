<?php

use Carbon\Carbon;
use App\Incrudible\Models\Admin;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

it('displays the profile page', function () {
    actingAs($this->admin, incrudible_guard_name())
        ->get(incrudible_route('profile.edit'))
        ->assertStatus(200);
});

it('allows admins to update their profile', function () {
    actingAs($this->admin, incrudible_guard_name())
        ->from(incrudible_route('profile.edit'))
        ->patch(incrudible_route('profile.update'), [
            'email' => 'updated@me.com',
            'username' => 'updated',
        ])
        ->assertRedirect(incrudible_route('profile.edit'));

    $this->admin->refresh();
    expect($this->admin->email)->toBe('updated@me.com');
    expect($this->admin->username)->toBe('updated');
    expect($this->admin->email_verified_at)->toBeNull();
});

it('leaves the email verification status unchanged when the email address is unchanged', function () {
    $this->markTestIncomplete('Email verification was removed since admins can only be created manually via cli...');
    actingAs($this->admin, incrudible_guard_name())
        ->from(incrudible_route('profile.edit'))
        ->patch(incrudible_route('profile.update'), [
            'email' => $this->admin->email,
            'username' => 'updated',
        ])
        ->assertRedirect(incrudible_route('profile.edit'));

    $this->admin->refresh();
    expect($this->admin->email_verified_at)->toBeInstanceOf(Carbon::class);
});
