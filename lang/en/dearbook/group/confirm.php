<?php

declare(strict_types=1);

return [
    'delete_option' => [
        'header' => 'Delete group',
        'intro' => 'Once your group is deleted, all of its resources and data will be permanently deleted. Before deleting your group, please, download any data or information that you wish to retain.',
        'modal' => [
            'question' => 'Are you sure you want to delete your group?',
            'text' => 'Once your group is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your group.',
        ],
    ],

    'delete_member_option' => [
        'modal' => [
            'question' => 'Member expulsion process',
            'text' => 'You are about to expel the member ":user_name" from the group. Confirm to continue with the process.',
            'btn_text' => 'Expel member',
        ],
    ],

    'leave_option' => [
        'modal' => [
            'question' => 'Leave group process',
            'text' => 'You are about to stop being a member of this group. You will no longer be able to manage any posts and comments you have made within the group. Confirm to continue with the process.',
            'btn_text' => 'Leave group',
        ],
    ],
];
