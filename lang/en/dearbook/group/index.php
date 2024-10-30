<?php

declare(strict_types=1);

return [
    'section_label' => 'Groups',

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
];
