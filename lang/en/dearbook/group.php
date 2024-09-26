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
        'extra' => [
            'date_of_creation' => 'Date of creation:',
            'created_by' => 'Created by:',
            'you' => 'you',
        ],
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
        'title_list_members' => '{1} List member|[2,*] List members',
        'group_by' => 'Group by',
        'type' => [
            'public' => 'Public group',
            'private' => 'Private group',
        ],
        'see_more_members' => 'See more members',
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

    'delete_option' => [
        'header' => 'Delete group',
        'intro' => 'Once your group is deleted, all of its resources and data will be permanently deleted. Before deleting your group, please, download any data or information that you wish to retain.',
        'confirmation' => [
            'question' => 'Are you sure you want to delete your group?',
            'text' => 'Once your group is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your group.',
        ],
    ],

    'mail' => [
        'invitation_to_join_group' => [
            'subject' => 'Invitation to join group',
            'greeting' => 'How are you :user_name?',
            'opening_phrase' => ':admin_group has invited you to join the ":group_name" group.',
            'closing_phrase' => 'Attention!!... The link will expire :num_hours hours after receiving this message.',
            'btn_text' => 'Accept INVITATION',
        ],
        'invitation_approved_by_user' => [
            'subject' => 'New Member - Invitation approved by the user',
            'greeting' => 'What\'s up :admin_group?...',
            'opening_phrase' => ':user_name has agreed to join the ":group_name" group via the invitation sent to him.',
            'btn_text' => 'Access to GROUP',
            'notification' => 'Welcome to ":group_name", you have just joined by invitation.',
        ],
    ],
];
