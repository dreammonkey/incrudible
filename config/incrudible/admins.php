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
        'perPage' => [
            'default' => 25,
            'options' => [10, 25, 50, 100],
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
                    'required',
                    'min:8',
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
            //     'unique:admins,email,id',
            // ],
        ],
    ],
];
