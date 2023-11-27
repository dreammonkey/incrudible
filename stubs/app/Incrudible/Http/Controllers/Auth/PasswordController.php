<?php

namespace App\Incrudible\Http\Controllers\Auth;

use App\Incrudible\Http\Controllers\Controller;
use App\Incrudible\Http\Requests\Auth\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Incrudible\Incrudible\Facades\Incrudible;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Incrudible::admin()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
