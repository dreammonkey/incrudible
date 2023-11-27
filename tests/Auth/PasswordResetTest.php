<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\from;
use function Pest\Laravel\post;

use App\Incrudible\Models\Admin;
use Illuminate\Support\Facades\Notification;
use App\Incrudible\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
});

it('displays the password forgotten page', function () {
    get(incrudible_route('auth.password.request'))
        ->assertStatus(200);
});

it('sends a password reset link', function () {
    Notification::fake();
    from(incrudible_route('auth.password.request'))
        ->post(incrudible_route('auth.password.email', [
            'email' => $this->admin->email,
        ]))
        ->assertRedirect(incrudible_route('auth.password.request'));
    Notification::assertSentTo($this->admin, ResetPassword::class);
});

it('displays the password reset page', function () {
    Notification::fake();
    post(incrudible_route('auth.password.email'), [
        'email' => $this->admin->email,
    ]);
    Notification::assertSentTo($this->admin, ResetPassword::class, function ($notification) {
        get(incrudible_route('auth.password.reset', [
            'token' => $notification->token,
        ]))
            ->assertStatus(200);

        return true;
    });
});

it('resets the password when a valid token is provided', function () {
    Notification::fake();
    post(incrudible_route('auth.password.email'), [
        'email' => $this->admin->email,
    ]);
    Notification::assertSentTo($this->admin, ResetPassword::class, function ($notification) {
        post(incrudible_route('auth.password.store'), [
            'token' => $notification->token,
            'email' => $this->admin->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(incrudible_route('auth.login'));

        expect(Hash::check('new-password', $this->admin->refresh()->password))->toBeTrue();

        return true;
    });
});
