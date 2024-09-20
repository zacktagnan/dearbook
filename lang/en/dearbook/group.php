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
        'btn_init_creation' => 'Create new group',
    ],

    'search' => [
        'placeholder' => 'Type the group to search',
    ],

    'list' => [
        'no_registers' => 'You are not joined to any group.',
        'no_description' => 'Description not available yet.',
        'title' => [
            'role' => [
                'admin' => 'You\'re ADMIN of the group',
            ],
            'status' => [
                'pending' => 'Request pending to be approval',
            ],
        ],
    ],

    'general_info' => [
        'members' => 'members',
        'type' => [
            'public' => 'Public group',
            'private' => 'Private group',
        ],
    ],

    'tab_info' => [
        'info_block' => [
            'header' => 'Information about this group',
            'features' => [
                'public' => [
                    'title' => 'Public',
                    'description' => 'Anyone can see who is a member of the group and everything posted.',
                ],
                'private' => [
                    'title' => 'Private',
                    'description' => 'Only members can see who is a member of the group and what was posted.',
                ],
                'visible' => [
                    'title' => 'Visible',
                    'description' => 'Anyone can find the group.',
                ],
                'history' => [
                    'title' => 'History',
                    'description_1_2' => 'Group created on :date',
                    'description_2_2' => 'by',
                ],
            ],
        ],
        'members_block' => [
            'header' => 'Members',
            'admin_text' => 'is the group admin.',
        ],
    ],
];
