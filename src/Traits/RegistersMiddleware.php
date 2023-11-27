<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Routing\Router;
use Incrudible\Incrudible\Facades\Incrudible;

trait RegistersMiddleware
{
    public function registerMiddlewareGroup(Router $router): void
    {
        $middleware_key = Incrudible::middleware();
        $middleware_classes = Incrudible::middlewareClasses();

        foreach ($middleware_classes as $middleware_class) {
            $router->pushMiddlewareToGroup($middleware_key, $middleware_class);
        }
    }

    public function registerMiddlewareAliases(Router $router): void
    {
        $router->aliasMiddleware(
            'must-authenticate',
            \App\Incrudible\Http\Middleware\MustAuthenticate::class
        );
        $router->aliasMiddleware(
            'must-confirm-password',
            \App\Incrudible\Http\Middleware\MustConfirmPassword::class
        );
    }
}
