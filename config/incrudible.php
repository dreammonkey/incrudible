<?php

// config for Incrudible/Incrudible
return [

    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    */

    // The namespace used in all generated files
    'namespace' => 'App\Incrudible',

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    // The prefix used in all base routes (the 'incrudible' in incrudible/dashboard)
    // You can make sure all your URLs use this prefix by using the incrudible_route() helper instead of route()
    'route_prefix' => 'incrudible',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [

        // Fully qualified namespace of the Admin model
        'user_model_fqn' => App\Incrudible\Models\Admin::class,

        // The classes for the middleware to check if the visitor is an admin
        'middleware_classes' => [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Incrudible\Http\Middleware\HandleIncrudibleRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        // Alias for that middleware
        // Use incrudible_middleware() helper function to retrieve this key
        'middleware_key' => 'incrudible',

        // The guard that protects the incrudible admin panel.
        // Use incrudible_guard_name() helper function to retrieve this key
        'guard' => 'incrudible',

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    */

    'menu' => [

        // The menu items that will be displayed in the sidebar
        'items' => [
            [
                'label' => 'Dashboard',
                'icon' => 'Gauge',
                'route' => 'dashboard',
            ],
            [
                'label' => 'Admin management',
                'icon' => 'ShieldAlert',
                'items' => [
                    [
                        'label' => 'Admins',
                        'icon' => 'Users',
                        'route' => 'admins.index',
                    ],
                    [
                        'label' => 'Roles',
                        'icon' => 'Shield',
                        'route' => 'roles.index',
                    ],
                    [
                        'label' => 'Permissions',
                        'icon' => 'Lock',
                        'route' => 'permissions.index',
                    ],
                ],
            ],
            [
                'label' => 'Settings',
                'icon' => 'Settings',
                'items' => [
                    [
                        'label' => 'Profile',
                        'icon' => 'User',
                        'route' => 'profile.edit',
                    ],
                    [
                        'label' => 'Settings',
                        'icon' => 'Settings2',
                        'route' => 'settings',
                    ],
                    [
                        'label' => 'Logout',
                        'icon' => 'LogOut',
                        'route' => 'logout',
                    ],
                ],
            ]
        ],

        // The menu items that will be displayed in the top right corner
        'top_right_items' => [
            [
                'label' => 'Profile',
                'route' => 'profile.edit',
            ],
            [
                'label' => 'Logout',
                'route' => 'logout',
            ],
        ],

    ],


];
