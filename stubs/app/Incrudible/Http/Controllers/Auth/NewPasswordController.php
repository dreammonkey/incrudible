<?php

namespace App\Incrudible\Http\Controllers\Auth;

use App\Incrudible\Http\Controllers\Controller;
use App\Incrudible\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(NewPasswordRequest $request): RedirectResponse
    {
        // Here we will attempt to reset the admin's password. If it is successful we
        // will update the password on an actual admin model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker(incrudible_guard_name())
            ->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($admin) use ($request) {
                    $admin->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($admin));
                }
            );

        // If the password was successfully reset, we will redirect the admin back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()
                ->to(incrudible_route('auth.login'))
                ->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
