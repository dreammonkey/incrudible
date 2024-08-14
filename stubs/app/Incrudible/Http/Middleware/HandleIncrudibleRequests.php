<?php

namespace App\Incrudible\Http\Middleware;

use Illuminate\Http\Request;
use Incrudible\Incrudible\Facades\Incrudible;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleIncrudibleRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'incrudible::incrudible';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $admin = Incrudible::check()
            ? Incrudible::admin()->toResource() : null;

        return [
            ...parent::share($request),
            'auth' => [
                'admin' => $admin,
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'query' => $request->query(),
            ],
            'incrudible' => [
                ...(Incrudible::toArray()),
            ],
        ];
    }
}
