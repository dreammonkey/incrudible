<?php

namespace App\Incrudible\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * A secured page.
     */
    public function settings(Request $request): Response|RedirectResponse
    {
        return Inertia::render('Settings');
    }
}
