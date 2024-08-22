<?php

return [
    //
    'index' => [
        'listable' => [
            'username',
            'email',
        ],
        'searchable' => [
            'username',
            'email',
        ],
        'sortable' => [
            'id',
            'username',
            'email',
            'created_at',
            'updated_at',
        ],
        'paging' => [
            'default' => 10,
            'options' => [10, 25, 50, 100],
        ],
        'create' => true,
        'actions' => [
            [
                'label' => 'View',
                'icon' => 'Eye',
                'action' => 'show',
                'type' => 'link',
            ],
            [
                'label' => 'Edit',
                'icon' => 'Edit',
                'action' => 'edit',
                'type' => 'link',
            ],
            [
                'label' => 'Delete',
                'icon' => 'Trash',
                'action' => 'destroy',
                'variant' => 'destructive',
                'type' => 'button',
            ],
        ],
    ],

    //
    'show' => [],

    //
    'store' => [
        'fields' => [
            [
                'name' => 'username',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Username',
                'placeholder' => 'Username',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
            [
                'name' => 'email',
                'type' => App\Incrudible\Enum\FieldTypes::EMAIL,
                'label' => 'Email',
                'placeholder' => 'Email',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
            [
                'name' => 'password',
                'type' => App\Incrudible\Enum\FieldTypes::PASSWORD,
                'label' => 'Password',
                'placeholder' => 'Password',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
            [
                'name' => 'password_confirmation',
                'type' => App\Incrudible\Enum\FieldTypes::PASSWORD,
                'label' => 'Password Confirmation',
                'placeholder' => 'Password Confirmation',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                    'required',
                    'min:8',
                    'same:password',
                ],
            ],
        ],
        'rules' => [
            'username' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'email',
                'unique:admins,email',
            ],
            'password' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'required',
                'min:8',
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'required',
                'min:8',
                'same:password',
            ],
        ],
    ],

    //
    'update' => [
        'fields' => [
            [
                'name' => 'username',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Username',
                'placeholder' => 'Username',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
            // [
            //     'name' => 'email',
            //     'type' => App\Incrudible\Enum\FieldTypes::EMAIL,
            //     'label' => 'Email',
            //     'placeholder' => 'Email',
            //     'options' => null,
            //     'required' => true,
            //     'rules' => [
            //         'required',
            //         'string',
            //         'min:1',
            //         'max:255',
            //     ],
            // ],
        ],
        'rules' => [
            'username' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            // 'email' => [
            //     'required',
            //     'string',
            //     'min:1',
            //     'max:255',
            //     'email',
            //     'unique:admins,email',
            // ],
        ],
    ],

    //
    'relations' => [
        [
            'name' => 'roles',
            'type' => 'BelongsToMany',
            'route' => 'admins.roles',
            'idKey' => 'id',
            'labelKey' => 'name',
        ],
    ],
];
