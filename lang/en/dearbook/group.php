<?php

declare(strict_types=1);

return [
    'section_label' => 'Groups',

    'create' => [
        'fields' => [
            'name' => [
                'placeholder' => 'Group name',
            ],
            'auto_approval' => [
                'label' => 'Auto approval',
            ],
            'about' => [
                'placeholder' => 'Short description',
            ],
        ],
        'btn_init_creation' => 'Create new group'
    ],

    'search' => [
        'placeholder' => 'Type the group to search'
    ],

    'list' => [
        'no_registers' => 'You are not joined to any group.'
    ],
];
