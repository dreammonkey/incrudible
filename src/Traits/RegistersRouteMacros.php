<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait RegistersRouteMacros
{
    public function registerRouteMacros(): void
    {
        Redirect::macro('incrudible_route', function ($name = null, $parameters = [], $status = 302, $headers = []) {
            return Redirect::to(incrudible_route($name, $parameters), $status, $headers);
        });

        /**
         * Register the associate route macro for usage with BelongsToMany relationships.
         * Usage: Route::associate('admins.roles', AdminRoleController::class);
         */
        Route::macro('associate', function (string $name, string $controller): void {
            // Split the name into parts
            $parts = explode('.', $name);
            // Remove the last part, it's the resource
            $resource = array_pop($parts);

            // Generate route segments and parameters
            $pathSegments = [];

            foreach ($parts as $part) {
                $singularPart = Str::singular($part);
                $pathSegments[] = "{$part}/{{$singularPart}}";
            }

            // Create the base path
            $pathPrefix = implode('/', $pathSegments);

            // The options route
            Route::get("$pathPrefix/options", [$controller, 'options'])
                ->name("$name.options");

            // The value route
            Route::get("$pathPrefix/$resource", [$controller, 'value'])
                ->name("$name.value");

            // The update route
            Route::put("$pathPrefix/$resource", [$controller, 'update'])
                ->name("$name.update");
        });
    }
}
