<?php

declare(strict_types=1);

return [
    'deleted_by_admin' => [
        'mailing' => [
            'subject' => 'Post deleted by ADMIN group',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => 'An ADMIN of the ":group_name" group has deleted your post.',
            'btn_text' => 'Access to GROUP',
        ],
    ],

    'created_on_group' => [
        'mailing' => [
            'subject' => 'Post created in ":group_name"',
            'greeting' => 'What\'s up member of ":group_name"?...',
            'opening_phrase' => 'In the group you are part of, a new post has been added.',
            'btn_text' => 'View Post',
        ],
    ],
];
