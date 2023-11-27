<?php

namespace App\Incrudible\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
