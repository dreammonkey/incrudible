<?php

namespace Incrudible\Incrudible\Traits;

use Incrudible\Incrudible\Facades\Incrudible;

trait RegistersAuthProvider
{
    /*
     * Incrudible login differs from the standard Laravel login.
     * As such, Incrudible uses its own authentication provider, password broker and guard.
     *
     * The process below adds those configuration values on top of whatever is in config/auth.php.
     * Developers can overwrite the incrudible provider, password broker or guard by adding a
     * provider/broker/guard with the "incrudible" name inside their config/auth.php file.
     * Or they can use another provider/broker/guard entirely, by changing the corresponding
     * incrudible.auth.guard value inside config/incrudible.php
     */
    public function registerAuthProvider()
    {
        // add the incrudible_users authentication provider to the configuration
        app()->config['auth.providers'] = app()->config['auth.providers'] +
            [
                Incrudible::guardName() => [
                    'driver'  => 'eloquent',
                    'model'   => config('incrudible.auth.user_model_fqn'),
                ],
            ];

        // add the incrudible guard to the configuration
        app()->config['auth.guards'] = app()->config['auth.guards'] +
            [
                Incrudible::guardName() => [
                    'driver'   => 'session',
                    'provider' => Incrudible::guardName(),
                ],
            ];

        // add the incrudible password provider to the configuration
        app()->config['auth.passwords'] = app()->config['auth.passwords'] +
            [
                Incrudible::guardName() => [
                    'provider' => Incrudible::guardName(),
                    'table' => 'password_reset_tokens',
                    'expire' => 60,
                    'throttle' => 60,
                ],
            ];
    }
}
