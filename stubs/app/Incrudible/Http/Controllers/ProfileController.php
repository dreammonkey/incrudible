<?php

namespace App\Incrudible\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Incrudible\Incrudible\Facades\Incrudible;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Incrudible\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => Incrudible::admin() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Incrudible::admin()->fill($request->validated());

        if (Incrudible::admin()->isDirty('email')) {
            Incrudible::admin()->email_verified_at = null;
        }

        Incrudible::admin()->save();

        return redirect()->to(incrudible_route('profile.edit'));
    }
}
