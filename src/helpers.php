<?php

if (! function_exists('incrudible_route')) {

    /**
     * Generate the URL to a named and prefixed incrudible route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    function incrudible_route($name = null, $parameters = [], $absolute = true): string
    {
        return route(incrudible_route_prefix().'.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('incrudible_route_prefix')) {

    /**
     * Returns the route prefix from the incrudible config.
     */
    function incrudible_route_prefix(): string
    {
        return config('incrudible.route_prefix', 'incrudible');
    }
}

if (! function_exists('incrudible_middleware')) {
    /**
     * Return the key of the middleware used across incrudible.
     * That middleware checks if the visitor is an admin.
     *
     * @param  $path
     */
    function incrudible_middleware(): string
    {
        return config('incrudible.auth.middleware_key', 'incrudible');
    }
}

if (! function_exists('incrudible_guard_name')) {
    /*
     * Returns the name of the guard defined
     * by the application config
     */
    function incrudible_guard_name(): string
    {
        return config('incrudible.auth.guard', 'incrudible');
    }
}
