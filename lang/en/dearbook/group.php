<?php

declare(strict_types=1);

return [
    'section_label' => 'Groups',

    'form' => [
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
        'btn_init_creation' => 'Create new group',
        'btn_init_update' => 'Update group',
        'btn_init_updated' => 'Updated.',
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
        // 'x_members' => ':total members',
        'x_members' => '{0} :total members|{1} :total member|[2,*] :total members',
        'type' => [
            'public' => 'Public group',
            'private' => 'Private group',
        ],
    ],

    'tab_info' => [
        'info_block' => [
            'header' => 'Information about this group',
            'intro' => 'Update your group data in a way that suits you best.',
            'no_intro' => '(introductory description not available)',
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
                    'description' => [
                        '1_3' => 'Group created on :date',
                        '2_3' => 'by',
                        '3_3' => 'you',
                    ],
                ],
            ],
        ],
        'members_block' => [
            'header' => 'Members',
            'admin_text' => 'is the group admin.',
        ],
    ],
];
