<?php

declare(strict_types=1);

return [
    'process_to_join' => [
        'by_invitation' => [
            'mailing' => [
                'to_join_group' => [
                    'subject' => 'Invitation to join group',
                    'greeting' => 'How are you :user_name?',
                    'opening_phrase' => ':admin_group has invited you to join the ":group_name" group.',
                    'closing_phrase' => 'Attention!!... The link will expire :num_hours hours after receiving this message.',
                    'btn_text' => 'Accept INVITATION',
                ],
                'approved_by_user' => [
                    'subject' => 'New Member - Invitation approved by the user',
                    'greeting' => 'What\'s up :admin_group?...',
                    'opening_phrase' => ':user_name has agreed to join the ":group_name" group via the invitation sent to him.',
                    'btn_text' => 'Access to GROUP',
                ],
            ],
            'web' => 'Welcome to ":group_name", you have just joined by invitation.',
        ],
        'by_auto_join' => [
            'web' => 'Welcome to ":group_name" ... good decision on your part.',
        ],
        'by_request' => [
            'mailing' => [
                'request_to_join_group' => [
                    'subject' => 'Request to join group',
                    'greeting' => 'What\'s up :admin_group?...',
                    'opening_phrase' => ':user_name has requested to join the group ":group_name".',
                    'btn_text' => 'Go to Approve REQUEST',
                ],
            ],
            'web' => 'Request sended. You will receive an email when it be approved.',
        ],
        'request_approved_or_not' => [
            'decision' => [
                'approved' => 'approved',
                'rejected' => 'rejected',
            ],
            'mailing' => [
                'request_approved_or_not' => [
                    'subject' => 'Request to join group',
                    'greeting' => 'What\'s up :user_name?...',
                    'opening_phrase' => 'Your request to join the group ":group_name" was :status.',
                    'closing_phrase' => [
                        'approved' => 'Congratulations!',
                        'rejected' => 'Sorry.',
                    ],
                    'btn_text' => 'Access to GROUP',
                ],
            ],
            'web' => 'The ":user_name" request was :status.',
        ],
    ],

    'member_role_change' => [
        'mailing' => [
            'subject' => 'ROLE change in the group',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'You have been assigned the role of ":role" dentro del grupo ":group_name".',
            'btn_text' => 'Access to GROUP',
        ],
        'web' => 'Successful change of Role to ":role" for :user_name.',
    ],

    'member_removed' => [
        'mailing' => [
            'subject' => 'Expelled from group',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'You have been expelled from the group ":group_name". We are very sorry.',
            'btn_text' => 'Access to GROUP',
        ],
        'web' => 'Successful expulsion of the member :user_name.',
    ],

    'member_leaving' => [
        'web' => 'You have unsubscribed from the :group_name group.',
    ],
];
