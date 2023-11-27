<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Facades\Redirect;

trait RegistersRouteMacros
{
    public function registerRouteMacros(): void
    {
        Redirect::macro('incrudible_route', function ($name = null, $parameters = [], $status = 302, $headers = []) {
            return Redirect::to(incrudible_route($name, $parameters), $status, $headers);
        });
    }
}
