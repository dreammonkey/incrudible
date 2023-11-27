<?php

namespace App\Incrudible\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

trait MustConfirmPassword
{
    public function confirmPassword(): ?RedirectResponse
    {
        if (!session()->has('auth.password_confirmed_at')) {

            session()->put('secured.intended', Route::current()->uri());

            return redirect()->to(incrudible_route('password.confirm'));
        }
    }
}
