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

    'created' => [
        'mailing' => [
            'subject' => 'Post created by :author_name',
            'greeting' => 'What\'s up follower?...',
            'opening_phrase' => ':author_name, one of your following, has just create a new post.',
        ],
    ],

    'created_on_group' => [
        'mailing' => [
            'subject' => 'Post created in ":group_name"',
            'greeting' => 'What\'s up member of ":group_name"?...',
            'opening_phrase' => 'In the group you are part of, a new post has been added by :author_name.',
        ],
    ],

    'created_on_group_or_not' => [
        'mailing' => [
            'btn_text' => 'View POST',
        ],
    ],

    'added_reaction' => [
        'mailing' => [
            'subject' => 'Reaction received by your post',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => ':user_reaction_name has issued a reaction of ":type" on your post.',
            'btn_text' => 'View POST',
        ],
    ],

    'updated_reaction' => [
        'mailing' => [
            'subject' => 'Reaction updated on your post',
            'greeting' => 'What\'s up :user_name?...',
            'opening_phrase' => ':user_reaction_name has modified his reaction to ":type" on your post.',
            'btn_text' => 'View POST',
        ],
    ],
];
