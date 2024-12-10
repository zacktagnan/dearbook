<?php

declare(strict_types=1);

return [
    'create' => [
        'fields' => [
            'name' => [
                'label' => 'Group name',
                'placeholder' => 'Group name',
            ],
            'auto_approval' => [
                'label' => 'Auto approval',
            ],
            'type' => [
                'label' => [
                    'public' => 'Public',
                    'private' => 'Private',
                ],
            ],
            'about' => [
                'label' => 'Short description',
                'placeholder' => 'Short description',
            ],
        ],
        'btn_reset' => 'Reset',
        'btn_init_creation' => 'Create new group',
        'btn_init_update' => 'Update group',
        'btn_init_updated' => 'Updated.',
        'extra' => [
            'date_of_creation' => 'Creation day:',
            'created_by' => 'By:',
            'you' => 'you',
        ],
    ],

    'invite_user' => [
        'fields' => [
            'username_or_email' => [
                'placeholder' => 'Username or Email',
            ],
        ],
    ],
];
