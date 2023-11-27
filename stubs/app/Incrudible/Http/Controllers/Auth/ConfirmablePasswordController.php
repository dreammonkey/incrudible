<?php

namespace App\Incrudible\Http\Controllers\Auth;

use App\Incrudible\Http\Controllers\Controller;
use App\Incrudible\Http\Requests\Auth\PasswordConfirmRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    /**
     * Confirm the user's password.
     */
    public function store(PasswordConfirmRequest $request): RedirectResponse
    {
        session()->put('auth.password_confirmed_at', time());

        $redirect = session()->has('must-confirm-password.intended')
            ? session()->get('must-confirm-password.intended')
            : incrudible_route('dashboard');

        session()->forget('must-confirm-password.intended');

        return redirect()->intended($redirect);
    }
}
