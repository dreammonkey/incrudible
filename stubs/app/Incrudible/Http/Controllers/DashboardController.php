<?php

namespace App\Incrudible\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class DashboardController
{
    public function dashboard(Request $request): Response
    {
        return Inertia::render('Dashboard', []);
    }

    /**
     * Redirect route at 'http://localhost/incrudible' to the dashboard
     */
    public function redirect(): RedirectResponse
    {
        return redirect()->incrudible_route('dashboard');
    }
}
