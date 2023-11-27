<?php

namespace App\Incrudible\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Incrudible\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * A secured page.
     */
    public function settings(Request $request): Response | RedirectResponse
    {
        return Inertia::render('Settings');
    }
}
