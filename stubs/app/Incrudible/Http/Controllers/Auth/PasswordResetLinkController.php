<?php

namespace App\Incrudible\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use App\Incrudible\Http\Controllers\Controller;
use App\Incrudible\Http\Requests\Auth\PasswordResetLinkRequest;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        $email = $request->validated('email');

        // We will send the password reset link to this admin. Once we have attempted
        // to send the link, we wil redirect the admin back to the login page,
        // regardless of whether or not an admin record exists with given email.
        $status = Password::broker(incrudible_guard_name())
            ->sendResetLink([
                'email' => $email,
            ]);

        Log::info("Password reset link requested for email: {$email}, status: {$status}");

        return back()->with('status', 'If there is an account for this user a password reset link has been sent.');
    }
}
