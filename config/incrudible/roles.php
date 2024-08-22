<?php

return [
    //
    'index' => [
        'listable' => [
            'name',
            'guard_name',
        ],
        'searchable' => [
            'name',
            'guard_name',
        ],
        'sortable' => [
            'id',
            'name',
            'guard_name',
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
                'type' => 'link'
            ],
            [
                'label' => 'Edit',
                'icon' => 'Edit',
                'action' => 'edit',
                'type' => 'link'
            ],
            [
                'label' => 'Delete',
                'icon' => 'Trash',
                'action' => 'destroy',
                'variant' => 'destructive',
                'type' => 'button'
            ],
        ],
    ],

    //
    'show' => [],

    //
    'store' => [
        'fields' => [
            [
                'name' => 'name',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Name',
                'placeholder' => 'Name',
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
                'name' => 'guard_name',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Guard_name',
                'placeholder' => 'Guard_name',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
        ],
        'rules' => [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'guard_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
        ],
    ],

    //
    'update' => [
        'fields' => [
            [
                'name' => 'name',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Name',
                'placeholder' => 'Name',
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
                'name' => 'guard_name',
                'type' => App\Incrudible\Enum\FieldTypes::TEXT,
                'label' => 'Guard_name',
                'placeholder' => 'Guard_name',
                'options' => null,
                'required' => true,
                'rules' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                ],
            ],
        ],
        'rules' => [
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'guard_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
        ],
    ],

    // TODO: relations must be added manually
    'relations' => [
        [
            'name' => 'permissions',
            'type' => 'BelongsToMany',
            'route' => 'roles.permissions',
            'idKey' => 'id',
            'labelKey' => 'name',
        ],
    ],
];
