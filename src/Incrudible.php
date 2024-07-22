<?php

namespace Incrudible\Incrudible;

use App\Incrudible\Models\Admin;
use Illuminate\Support\Facades\Auth;

class Incrudible
{
    /**
     * Check if the incrudible config was published.
     */
    public function configNotPublished(): bool
    {
        return is_null(config('incrudible'));
    }

    /**
     * Get the currently authenticated admin.
     */
    public function admin(): ?Admin
    {
        return Auth::guard(self::guardName())->user();
    }

    /**
     * Determine if the current admin is authenticated.
     */
    public function check(): bool
    {
        return Auth::guard(self::guardName())->check();
    }

    /**
     * Get the incrudible route prefix.
     */
    public function routePrefix(): string
    {
        // dd(config('incrudible.route_prefix'))
        return config('incrudible.route_prefix', 'incrudible');
    }

    /**
     * Get the incrudible middleware identifier.
     */
    public function middleware(): string
    {
        return config('incrudible.auth.middleware_key', 'incrudible');
    }

    /**
     * Get the incrudible middleware classes.
     */
    public function middlewareClasses(): array
    {
        return config('incrudible.auth.middleware_classes', []);
    }

    /**
     * Get the incrudible authentication guard name.
     */
    public function guardName(): string
    {
        return config('incrudible.auth.guard', 'incrudible');
    }

    /**
     * Get the incrudible menu items.
     */
    public function menu(): array
    {
        return config('incrudible.menu', []);
    }

    /**
     * Convert this Incrudible class to an array.
     */
    public function toArray(): array
    {
        return [
            'routePrefix' => self::routePrefix(),
            'menu' => self::menu(),
        ];
    }
}
